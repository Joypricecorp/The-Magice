<?php
namespace Magice\Session\Handler {

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Pdo implements \SessionHandlerInterface
    {
        /**
         * @var \PDO PDO instance.
         */
        private $pdo;

        /**
         * @var array Database options.
         */
        private $dbOptions;

        /**
         * Constructor.
         * List of available options:
         *  * name: The name of the table [required]
         *  * col_id: The column where to store the session id [default: ssn_id]
         *  * col_data: The column where to store the session data [default: ssn_data]
         *  * col_time: The column where to store the timestamp [default: ssn_time]
         *
         * @param \PDO|PdoService  $pdo       A \PDO instance
         * @param array|string $dbOptions An associative array of DB options or just table name
         *
         * @throws \InvalidArgumentException When "table" option is not provided
         */
        public function __construct($pdo, $dbOptions = null)
        {
            if(is_string($dbOptions)) {
                $dbOptions = array('name' => $dbOptions);
            }

            if (!array_key_exists('name', $dbOptions)) {
                throw new \InvalidArgumentException('You must provide the "table:name" option for a PdoSessionStorage.');
            }

            if($pdo instanceof PdoService) {
                $pdo = $pdo->getPdo();
            }
            
            if (\PDO::ERRMODE_EXCEPTION !== $pdo->getAttribute(\PDO::ATTR_ERRMODE)) {
                throw new \InvalidArgumentException(sprintf(
                    '"%s" requires PDO error mode attribute be set to throw Exceptions (i.e. $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION))',
                    __CLASS__
                ));
            }

            $this->pdo       = $pdo;
            $this->dbOptions = array_merge(
                array(
                    'col_id'   => 'ssn_id',
                    'col_data' => 'ssn_data',
                    'col_time' => 'ssn_time',
                ),
                $dbOptions
            );
        }

        /**
         * {@inheritDoc}
         */
        public function open($path, $name)
        {
            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function close()
        {
            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function destroy($id)
        {
            // delete the record associated with this id
            $sql = sprintf(
                "DELETE FROM %s WHERE %s = :id",
                $this->dbOptions['name'],
                $this->dbOptions['col_id']
            );

            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                $stmt->execute();
            } catch (\PDOException $e) {
                throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
            }

            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function gc($lifetime)
        {
            // delete the session records that have expired
            $sql = sprintf(
                "DELETE FROM %s WHERE %s < :time",
                $this->dbOptions['name'],
                $this->dbOptions['col_time']
            );

            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':time', time() - $lifetime, \PDO::PARAM_INT);
                $stmt->execute();
            } catch (\PDOException $e) {
                throw new \RuntimeException(sprintf('PDOException was thrown when trying to manipulate session data: %s', $e->getMessage()), 0, $e);
            }

            return true;
        }

        /**
         * {@inheritDoc}
         */
        public function read($id)
        {
            try {
                $sql = sprintf(
                    "SELECT %s FROM %s WHERE %s = :id",
                    $this->dbOptions['col_data'],
                    $this->dbOptions['name'],
                    $this->dbOptions['col_id']
                );

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':id', $id, \PDO::PARAM_STR);

                $stmt->execute();
                // it is recommended to use fetchAll so that PDO can close the DB cursor
                // we anyway expect either no rows, or one row with one column. fetchColumn, seems to be buggy #4777
                $sessionRows = $stmt->fetchAll(\PDO::FETCH_NUM);

                if (count($sessionRows) == 1) {
                    return base64_decode($sessionRows[0][0]);
                }

                // session does not exist, create it
                $this->createNewSession($id);

                return '';
            } catch (\PDOException $e) {
                throw new \RuntimeException(sprintf('PDOException was thrown when trying to read the session data: %s', $e->getMessage()), 0, $e);
            }
        }

        /**
         * {@inheritDoc}
         */
        public function write($id, $data)
        {
            // get table/column
            $dbTable   = $this->dbOptions['name'];
            $dbDataCol = $this->dbOptions['col_data'];
            $dbIdCol   = $this->dbOptions['col_id'];
            $dbTimeCol = $this->dbOptions['col_time'];

            //session data can contain non binary safe characters so we need to encode it
            $encoded = base64_encode($data);

            try {
                $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

                if ('mysql' === $driver) {
                    // MySQL would report $stmt->rowCount() = 0 on UPDATE when the data is left unchanged
                    // it could result in calling createNewSession() whereas the session already exists in
                    // the DB which would fail as the id is unique
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO $dbTable ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, :time) " .
                        "ON DUPLICATE KEY UPDATE $dbDataCol = VALUES($dbDataCol), $dbTimeCol = VALUES($dbTimeCol)"
                    );
                    $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                    $stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
                    $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
                    $stmt->execute();
                } elseif ('oci' === $driver) {
                    $stmt = $this->pdo->prepare(
                        "MERGE INTO $dbTable USING DUAL ON($dbIdCol = :id) " .
                        "WHEN NOT MATCHED THEN INSERT ($dbIdCol, $dbDataCol, $dbTimeCol) VALUES (:id, :data, sysdate) " .
                        "WHEN MATCHED THEN UPDATE SET $dbDataCol = :data WHERE $dbIdCol = :id"
                    );

                    $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                    $stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
                    $stmt->execute();
                } else {
                    $stmt = $this->pdo->prepare("UPDATE $dbTable SET $dbDataCol = :data, $dbTimeCol = :time WHERE $dbIdCol = :id");
                    $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
                    $stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
                    $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
                    $stmt->execute();

                    if (!$stmt->rowCount()) {
                        // No session exists in the database to update. This happens when we have called
                        // session_regenerate_id()
                        $this->createNewSession($id, $data);
                    }
                }
            } catch (\PDOException $e) {
                throw new \RuntimeException(sprintf('PDOException was thrown when trying to write the session data: %s', $e->getMessage()), 0, $e);
            }

            return true;
        }

        /**
         * Creates a new session with the given $id and $data
         *
         * @param string $id
         * @param string $data
         *
         * @return boolean True.
         */
        private function createNewSession($id, $data = '')
        {
            $sql = sprintf(
                "INSERT INTO %s (%s, %s, %s) VALUES (:id, :data, :time)",
                $this->dbOptions['name'],
                $this->dbOptions['col_id'],
                $this->dbOptions['col_data'],
                $this->dbOptions['col_time']
            );

            //session data can contain non binary safe characters so we need to encode it
            $encoded = base64_encode($data);
            $stmt    = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
            $stmt->bindParam(':data', $encoded, \PDO::PARAM_STR);
            $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
            $stmt->execute();

            return true;
        }

        /**
         * Return a PDO instance
         * @return \PDO
         */
        protected function getConnection()
        {
            return $this->pdo;
        }
    }
}