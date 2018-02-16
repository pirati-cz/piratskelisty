<?php

interface IUzivatele {
    public function add(\LightOpenID $openId);
    public function get($identity);
    public function getAll();
    public function getRole($id);
    public function getRolePairs();
    public function setRole($id, $roles);

}