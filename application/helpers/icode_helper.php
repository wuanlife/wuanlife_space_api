<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2017/3/8 0008
 * Time: 12:40
 */
defined('BASEPATH') OR exit('No direct script access allowed');
if ( ! function_exists('create_code'))
{
    function create_code($word_length='',$pool='')
    {
        if(empty($pool)){
            $pool = '123456789qwertyuiopasdfghjklzxcvbnmQAZXSWEDCVFRTGBNHYUJMKIOLP';
        }
        if (empty($word))
        {
            $word = '';
            $pool_length = strlen($pool);
            $rand_max = $pool_length - 1;
            if (function_exists('random_int'))
            {
                try
                {
                    for ($i = 0; $i < $word_length; $i++)
                    {
                        $word .= $pool[random_int(0, $rand_max)];
                    }
                }
                catch (Exception $e)
                {
                    $word = '';
                }
            }
        }

        if (empty($word))
        {
            if ($pool_length > 256)
            {
                return FALSE;
            }
            $security = get_instance()->security;
            if (($bytes = $security->get_random_bytes($pool_length)) !== FALSE)
            {
                $byte_index = $word_index = 0;
                while ($word_index < $word_length)
                {
                    if ($byte_index === $pool_length)
                    {
                        for ($i = 0; $i < 5; $i++)
                        {
                            if (($bytes = $security->get_random_bytes($pool_length)) === FALSE)
                            {
                                continue;
                            }

                            $byte_index = 0;
                            break;
                        }

                        if ($bytes === FALSE)
                        {
                            $word = '';
                            break;
                        }
                    }

                    list(, $rand_index) = unpack('C', $bytes[$byte_index++]);
                    if ($rand_index > $rand_max)
                    {
                        continue;
                    }

                    $word .= $pool[$rand_index];
                    $word_index++;
                }
            }
        }

        if (empty($word))
        {
            for ($i = 0; $i < $word_length; $i++)
            {
                $word .= $pool[mt_rand(0, $rand_max)];
            }
        }
        elseif ( ! is_string($word))
        {
            $word = (string) $word;
        }
        return $word;
    }
}