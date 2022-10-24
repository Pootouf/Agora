<?php

namespace AGORA\AdminPlatformBundle\Entity;


class TaskGenUser {
    protected $prefix;
    protected $password;
    protected $quantity;

    public function getPrefix() {
        return $this->prefix;
    }

    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    public function getQuantity() {
        return $this->quantity;
    }
}
