<?php
putenv("TEST=JJJ");
$_ENV["TEST"] = "barney";
echo getenv("TEST");
echo $_ENV["TEST"];
