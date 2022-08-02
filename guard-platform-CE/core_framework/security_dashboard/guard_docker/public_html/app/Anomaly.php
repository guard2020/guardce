<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Anomaly extends Eloquent
{

    protected $connection = 'mongodb';
    protected $collection = 'anomalies';




}
