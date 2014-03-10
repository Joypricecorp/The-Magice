<?php
namespace Magice\Orm\Entity {

    use Doctrine\DBAL\Types\Type;
    use Magice\Orm\Manager;

    class Structure
    {
        public static function create($entityName, Manager $manager)
        {
            $fields = array();
            $class  = new \ReflectionClass($entityName);

            foreach ($class->getProperties() as $var) {

                if ($var->isStatic()) {
                    continue;
                }

                $key  = $var->getName();
                $type = $manager->getClassMetadata($entityName)->getTypeOfField($key);

                switch ($type) {

                    case Type::DATE:
                        $fields[] = array(
                            'name'       => $key,
                            'type'       => 'date',
                            'dateFormat' => 'Y-m-d'
                        );
                        break;

                    case Type::TIME:
                        $fields[] = array(
                            'name'       => $key,
                            'type'       => 'date',
                            'dateFormat' => 'H:i:s'
                        );
                        break;

                    case Type::DATETIME:
                    case Type::DATETIMETZ:
                        $fields[] = array(
                            'name'       => $key,
                            'type'       => 'date',
                            'dateFormat' => 'Y-m-d H:i:s'
                        );
                        break;

                    case Type::BIGINT:
                    case type::INTEGER:
                        $fields[] = array(
                            'name' => $key,
                            'type' => 'int'
                        );
                        break;

                    case TYPE::FLOAT:
                    case TYPE::DECIMAL:
                        $fields[] = array(
                            'name' => $key,
                            'type' => 'float'
                        );
                        break;

                    case TYPE::BOOLEAN:
                        $fields[] = array(
                            'name' => $key,
                            'type' => 'boolean'
                        );
                        break;

                    case TYPE::JSON_ARRAY:
                    case TYPE::OBJECT:
                    case TYPE::SIMPLE_ARRAY:
                    case TYPE::TARRAY:
                        $fields[] = array(
                            'name' => $key,
                            'type' => 'auto'
                        );
                        break;

                    default:
                        $fields[] = array(
                            'name' => $key,
                            'type' => 'string'
                        );
                        break;
                }
            }

            return $fields;
        }
    }

}