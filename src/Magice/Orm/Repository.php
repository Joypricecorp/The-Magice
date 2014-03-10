<?php
namespace Magice\Orm {

    use Doctrine\ORM\EntityRepository;
    use Doctrine\ORM\Mapping\ClassMetadata;
    use Symfony\Component\DependencyInjection\ContainerInterface;
    use Psr\Log\LoggerInterface;

    class Repository extends EntityRepository
    {
        /**
         * @var string
         */
        protected $_entityName;

        /**
         * @var Manager
         */
        protected $_em;

        /**
         * @var ClassMetadata
         */
        protected $_class;

        /**
         * @var ContainerInterface
         */
        protected $_container;

        /**
         * @var LoggerInterface
         */
        protected $logger;

        /**
         * Initializes a new <tt>EntityRepository</tt>.
         *
         * @param Manager       $em    The EntityManager to use.
         * @param ClassMetadata $class The class descriptor.
         * @param ContainerInterface     $cn    The Service container
         */
        public function __construct(Manager $em, ClassMetadata $class, ContainerInterface $cn)
        {
            $this->_entityName = $class->name;
            $this->_em         = $em;
            $this->_class      = $class;
            $this->_container  = $cn;
            $this->logger = $cn->get('logger');
        }
    }
}