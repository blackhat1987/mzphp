<?php

class log
{
    /**
     * @var int log file name
     */
    public static $log_file = 0;
    /**
     * @var int log file file pointer
     */
    public static $log_fp = 0;

    /**
     * init to log
     *
     * @param $file
     */
    public static function set_logfile($file) {
        if ($file == 1) {
            $file = ROOT_PATH . 'data/log/' . date('Y-m-d') . '.log';
        }
        self::$log_file = $file;
        self::$log_fp   = fopen($file, 'a+');
    }

    /**
     * alias set log file
     *
     * @param $file
     */
    public static function set_file($file) {
        self::set_logfile($file);
    }

    /**
     * dump variable for log
     *
     * @param $data
     *
     * @return string
     */
    public static function dump_var($data) {
        if (is_array($data)) {
            $str = '';
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $str .= '[' . $k . '=' . self::dump_var($v) . ']';
                } else {
                    $str .= '[' . $k . '=' . $v . ']';
                }
            }
            return $str;
        } else {
            return '[' . $data . ']';
        }
    }

    /**
     * log::info($arg1,$arg2....$argn);
     *
     * @param mixed
     */
    public static function info() {
        self::add_log('info', func_get_args(), func_num_args());
    }

    /**
     * log::error($arg1,$arg2....$argn);
     *
     * @param mixed
     */
    public static function error() {
        self::add_log('error', func_get_args(), func_num_args());
        throw new Exception('error');
    }

    /**
     * add log
     *
     * @param $type
     * @param $arg_list
     * @param $arg_count
     */
    private static function add_log($type, $arg_list, $arg_count) {
        $log = '';
        for ($i = 0, $l = $arg_count; $i < $l; $i++) {
            $log .= self::dump_var($arg_list[$i]);
        }
        $usetime = core::usedtime();
        if ($usetime > 1000 * 3600) {
            $usetime = round($usetime / (1000 * 3600), 3) . 'h';
        } else if ($usetime > 1000 * 360) {
            $usetime = round($usetime / (1000 * 60), 3) . 'm';
        } else if ($usetime > 1000 * 60) {
            $usetime = round($usetime / 1000, 3) . 's';
        } else {
            $usetime = $usetime . 'ms';
        }
        $log .= '[' . $usetime . "]";
        $log = "[" . date('H:i:s') . "]" . $log . "\r\n";
        if (self::$log_fp) {
            fputs(self::$log_fp, $log);
        }
        if (core::is_cmd()) {
            echo $log;
        } else {
            if (isset($_SERVER['log'])) {
                $_SERVER['log'] = array(
                    'info'  => array(),
                    'error' => array(),
                );
            }
            $_SERVER['log'][$type][] = $log;
        }
    }
}

?>