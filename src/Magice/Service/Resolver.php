<?php
namespace Magice\Service {

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Resolver
    {
        /**
         * @var string NAME Name of target service.
         * @abstract
         */
        const NAME = 'You must implement (const NAME = "service_id") in your serviced class (%s).';

        /**
         * @var string Internal Magice Services Symbol
         */
        const MAGICE_SYMBOL = ':';

        /**
         * @var string Internal Magice Services Prefix
         */
        const MAGICE_PREFIX = 'magice.service.';

        /**
         * @var ContainerBasicInterface The instance of container
         */
        protected static $serviceContainer;

        /**
         * @var array The resolved services
         */
        protected static $serviceResolved = [];

        /**
         * setup container instance
         *
         * @param ContainerBasicInterface $container
         *
         * @return void
         */
        final public static function setupContainer(ContainerBasicInterface $container)
        {
            static::$serviceContainer = $container;
        }

        /**
         * Check service existing
         *
         * @param string $id Service Id
         *
         * @return bool
         */
        final public static function hasService($id)
        {
            return array_key_exists($id, static::$serviceResolved);
        }

        /**
         * Check defined service name with const NAME
         * @return string|void Name of service if defined NAME const in the class
         * @throws ServiceException
         */
        final public static function getServiceName()
        {
            $caller = new \ReflectionClass(get_called_class());
            $name   = $caller->getConstant('NAME');

            if (empty($name)) {
                throw new ServiceException(sprintf(self::NAME, $caller->getName()));
            }

            return str_replace(self::MAGICE_SYMBOL, self::MAGICE_PREFIX, $name);
        }

        final protected static function getService($id, $behavior = ContainerBasicInterface::INVALID_REFERENCE_EXCEPTION)
        {
            if (empty(static::$serviceContainer)) {
                throw new ServiceException('Did you forgot set Service Container?');
            }

            try {
                if (isset(static::$serviceResolved[$id])) {
                    $service = static::$serviceResolved[$id];
                } else {
                    $service = static::$serviceResolved[$id] = static::$serviceContainer->get($id, $behavior);
                }

                return $service;
            } catch (\Exception $e) {
                if ($behavior === ContainerBasicInterface::INVALID_REFERENCE_EXCEPTION) {
                    throw new ServiceException($e->getMessage(), $e->getCode());
                } else {
                    return null;
                }
            }
        }

        /**
         * Resolve the service by it's id
         *
         * @param string|null $name
         *
         * @return array
         * @throws ServiceException
         */
        final protected static function resolveService($name = null)
        {
            $name = $name ? : static::getServiceName();

            try {

                // try to get service by original name
                if ($service = self::getService($name, Container::INVALID_REFERENCE_IGNORE)) {
                    $member = null;
                } else {

                    /**
                     * @var string $name   service name
                     * @var string $member service member (property,method) to get access to
                     *                     to make explicitly method you can name.member() intead of name.member
                     * @note if have many . (namespace.service.member) assume last node is a member
                     */
                    $members = explode('.', str_replace(self::MAGICE_PREFIX, '', $name));

                    switch (count($members)) {
                        case 1:
                            $member = null;
                            break;

                        case 2:
                            $name   = $members[0];
                            $member = $members[1];
                            break;

                        default:
                            $member = array_pop($members);
                            $name   = implode('.', $members);
                            break;
                    }

                    $service = self::getService($name);
                }

                $_property = null;
                $_method   = null;

                if (preg_match_all('/\((.*)\)/', $member, $mts)) {
                    // todo parse params and auto hint-type (if have)
                    //$args = isset($mts[1][0]) ? $mts[1][0] : \Magice::VAR_UNDEF;

                    $_method = str_replace($mts[0][0], '', $member);
                } else {
                    if ($member) {
                        if (property_exists($service, $member)) {
                            $_property = $member;
                            $_method   = null;
                        } elseif (method_exists($service, $member)) {
                            $_method   = $member;
                            $_property = null;
                        } else {
                            throw new ServiceException(sprintf('Unknow member ("%s") of ("%s") service.', $member, $name));
                        }
                    }
                }

                if ($_property) {
                    $service = $service->$_property;
                }

                if ($_method) {
                    // todo insert args
                    $service = $service->$_method();
                }

                if (!is_object($service)) {
                    throw new ServiceException(sprintf(
                        'Cannot find member (%s%s) of %s is defined with %s.%s.',
                        $_property,
                        $_method,
                        $name,
                        $name,
                        $member
                    ));
                }

                return $service;

            } catch (\Exception $e) {
                throw new ServiceException($e->getMessage(), $e->getCode());
            }
        }

        /**
         * @param string $method
         * @param array  $args
         *
         * @return mixed
         * @throws ServiceException
         */
        final static function __callStatic($method, $args)
        {
            $service = static::resolveService();

            // todo better find and match args
            $return = null;
            switch (count($args)) {
                case 0:
                    $return = $service->$method();
                    break;

                case 1:
                    $return = $service->$method($args[0]);
                    break;

                case 2:
                    $return = $service->$method($args[0], $args[1]);
                    break;

                case 3:
                    $return = $service->$method($args[0], $args[1], $args[2]);
                    break;

                case 4:
                    $return = $service->$method($args[0], $args[1], $args[2], $args[3]);
                    break;

                default:
                    $return = call_user_func_array(array($service, $method), $args);
                    break;
            }

            return $return;
        }
    }
}