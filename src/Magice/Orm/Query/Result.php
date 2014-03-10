<?php
namespace Magice\Orm\Query {

    use Doctrine\ORM\AbstractQuery;
    use Doctrine\ORM\QueryBuilder as OrmQueryBuilder;
    use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
    use Doctrine\ORM\Tools\Pagination\Paginator;

    class Result implements ResultInterface
    {
        /**
         * @var OrmQueryBuilder|DbalQueryBuilder
         */
        protected $builder;

        public function __construct($builder)
        {
            $this->builder = $builder;
        }

        public function getArrays($asColumn = false)
        {
            if ($this->builder instanceof OrmQueryBuilder) {
                //return $this->builder->getQuery()->getArrayResult();
                return $this->builder->getQuery()->getResult(AbstractQuery::HYDRATE_SIMPLEOBJECT);
            }

            if ($this->builder instanceof DbalQueryBuilder) {
                /**
                 * @var \Doctrine\DBAL\Driver\Statement $smt ;
                 */
                //$smt = $this->builder->execute();
                trigger_error('TODO: ' . __FILE__ . ' ' . __LINE__);
            }
        }

        public function getPlains($asColumn = false)
        {
            // TODO: Implement getPlains() method.
        }

        public function getObjects()
        {
            if ($this->builder instanceof OrmQueryBuilder) {
                return $this->builder->getQuery()->getResult();
            }

            if ($this->builder instanceof DbalQueryBuilder) {
                throw new \RuntimeException('Native query mode cannot return entity object.');
            }
        }

        public function getPaginArrays($asColumn = false)
        {
            if ($this->builder instanceof OrmQueryBuilder) {
                $pagin = new Paginator($this->builder, $fetchJoinCollection = true);

                $rows = $pagin->getQuery()->getArrayResult();

                if ($asColumn) {
                    // TODO: improve me! by hydrator
                    $entityClass = $this->builder->getRootEntities()[0];
                    $columnNames = $this->builder->getEntityManager()->getClassMetadata($entityClass)->columnNames;

                    $rs = array();
                    foreach ($rows as $row) {
                        $r = array();

                        foreach ($columnNames as $k => $v) {
                            if (isset($row[$k])) {
                                $r[$v] = $row[$k];
                            }
                        }

                        $rs[] = $r;
                    }

                    $rows = $rs;
                }

                return array(
                    'total' => $pagin->count(),
                    'rows'  => $rows
                );
            }

            if ($this->builder instanceof DbalQueryBuilder) {
                /**
                 * @var \Doctrine\DBAL\Driver\Statement $smt ;
                 */
                //$smt = $this->builder->execute();
                trigger_error('TODO: ' . __FILE__ . ' ' . __LINE__);
            }

        }

        public function getPaginPlains($asColumn = false)
        {
            if ($this->builder instanceof OrmQueryBuilder) {
                $pagin = new Paginator($this->builder, $fetchJoinCollection = true);

                $rows = $pagin->getQuery()->getArrayResult();

                if ($asColumn) {
                    // TODO: improve me! by hydrator
                    $entityClass = $this->builder->getRootEntities()[0];
                    $columnNames = $this->builder->getEntityManager()->getClassMetadata($entityClass)->columnNames;

                    $rs = array();
                    foreach ($rows as $row) {
                        $r = array();

                        foreach ($columnNames as $k => $v) {
                            if (isset($row[$k])) {
                                $r[$v] = $row[$k];
                            }
                        }

                        $rs[] = (object) $r;
                    }

                    $rows = $rs;
                }

                return array(
                    'total' => $pagin->count(),
                    'rows'  => $rows
                );
            }

            if ($this->builder instanceof DbalQueryBuilder) {
                /**
                 * @var \Doctrine\DBAL\Driver\Statement $smt ;
                 */
                //$smt = $this->builder->execute();
                trigger_error('TODO: ' . __FILE__ . ' ' . __LINE__);
            }
        }

        public function getPaginObjects()
        {
            // TODO: Implement getPaginObjects() method.
        }

        public function getCommon($value, $text, $fieldValue = 'def_value', $fieldText = 'def_text')
        {
            $rows = array();
            if ($this->builder instanceof OrmQueryBuilder) {
                if ($all = $this->builder->getQuery()->getResult()) {
                    $getText  = 'get' . $text;
                    $getValue = 'get' . $value;

                    foreach ($all as $r) {
                        $rows[] = array(
                            $fieldText  => $r->$getText(),
                            $fieldValue => $r->$getValue()
                        );
                    }
                }
            }

            if ($this->builder instanceof DbalQueryBuilder) {
                throw new \RuntimeException('TODO: now not support');
            }

            return $rows;
        }
    }
}