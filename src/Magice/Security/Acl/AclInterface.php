<?php
namespace Magice\Security\Acl {

    interface AclInterface
    {
        public function isAdmin();
        public function isSuperAdmin();
        public function can();
    }
}