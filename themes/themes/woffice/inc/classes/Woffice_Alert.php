<?php
/**
 * Class Woffice_Alert
 * Use it like: Woffice_Alert::create()->setType('error')->setContent('Foobar')->queue();
 *
 * Used to create a Welcome page when Woffice is installed and activated.
 *
 * @since 2.5.0
 * @author Alkalab
 */

if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

if( ! class_exists( 'Woffice_Alert' ) ) {
    class Woffice_Alert
    {

        /**
         * @var Woffice_Alert
         */
        public static $instance = null;

        /**
         * @var string - id
         */
        public $id;

        /**
         * @var string - 'info', 'updated', 'success', 'error', 'warning', 'alert'
         */
        public $type;

        /**
         * @var string - HTML content
         */
        public $content;

        /**
         * @var string - CSS class
         */
        public $timing;

        /**
         * Set unique ID to the alert.
         */
        public function setId() {

            if(isset($this->id))
                return;

            $this->id = uniqid();

        }

        /**
         * Create a new alert
         *
         * @return Woffice_Alert
         */
        public static function create()
        {

            self::$instance = new static();

            return self::$instance;

        }

        /**
         * Set the alert type
         *
         * @param string $type
         * @return Woffice_Alert
         */
        public function setType($type = '')
        {

            $this->setId();

            $this->type = esc_attr($type);

            return $this;

        }

        /**
         * Set the alert content
         *
         * @param string $content
         * @return Woffice_Alert
         */
        public function setContent($content = '')
        {

            $this->setId();

            $this->content = $content;

            return $this;

        }

        /**
         * Add the current instance to the queue
         */
        public function queue()
        {

            if(!isset($_SESSION))
                session_start();

            $woffice_alerts = (isset($_SESSION['woffice_alerts'])) ? $_SESSION['woffice_alerts'] : array();

            $woffice_alerts[$this->id] = serialize($this);

            $_SESSION['woffice_alerts'] = $woffice_alerts;

            return $this;

        }

        /**
         * Remove an alert from the queue
         *
         * @param string $alert_id
         */
        static function remove($alert_id)
        {

            $woffice_alerts = (isset($_SESSION['woffice_alerts'])) ? $_SESSION['woffice_alerts'] : null;

            if(empty($alert_id) || empty($woffice_alerts))
                return;

            unset($woffice_alerts[$alert_id]);

            $_SESSION['woffice_alerts'] = $woffice_alerts;

        }

        /**
         * Flush all current alerts
         */
        static function flush()
        {

            $woffice_alerts = (isset($_SESSION['woffice_alerts'])) ? $_SESSION['woffice_alerts'] : null;

            if(empty($woffice_alerts))
                return;

            unset($_SESSION['woffice_alerts']);

        }

        /**
         * Configure the timeout
         *
         * @param string $timing
         * @return Woffice_Alert
         */
        public function setTimeout($timing = '') {

            if ($timing !== false)
                return;

            $this->timing = "no-timeout";

            //TODO: make this work for any timeout
            // We would basically pass it as a css class: time-xxxxx and get xxxx with the js
            //$this->timing = "time-".$timing;

            return $this;

        }

    }
}