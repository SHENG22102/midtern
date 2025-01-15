<?php

if (! isset($_SESSION)) {
  session_start();
};

require __DIR__. '/db-connect.php';