<?php
// Gadget class that is autoloadable/visible to unserialize() in index.php's cart module.
// so a forged serialized Coupon object => arbitrary file write (web-shell drop).
class Coupon {
    public $code = 'SAVE10';
    public $logfile = '/tmp/coupon.log';
    public $logdata = '';
    public function __destruct() {
        if ($this->logdata !== '') {
            file_put_contents($this->logfile, $this->logdata); // attacker-controlled sink
        }
    }
}
