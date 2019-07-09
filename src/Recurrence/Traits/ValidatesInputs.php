<?php

/*
 * Copyright 2019 Maxwell Mandela
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Based on simshaun/recurr
 * Copyright (c) 2015 Shaun Simmons
 * https://github.com/simshaun/recurr/blob/master/LICENSE
 *
 * Based on nesbot/carbon
 * Copyright (C) Brian Nesbitt
 * https://github.com/briannesbitt/Carbon/blob/master/LICENSE
 */

namespace Reccurence\Traits;


/**
 *  Validates the inputs for scheduling events
 * 
 *  Checks minimum requirements against supplied
 */
trait ValidatesInputs
{

    /**
     * Validates inputs
     * 
     * @param array $inputs
     */
    public function validateInputs($inputs)
    {
        $valid = 0;
        if (count($inputs) < self::MINIMUN_INPUTS) {
            return false;
        }

        foreach (self::INPUT_KEYS as $key) {
            if (array_key_exists($key, $inputs)) {
                $valid++;
            }
        }
        return count(self::INPUT_KEYS) == $valid;
    }
}
