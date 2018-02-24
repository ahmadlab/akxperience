<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Apns_model extends CI_Model
{

    protected $apnsDir = '';
    private $log_file = 'apn.log';

    // -----------------------------------------------

    /**
     * Setup some basic stuffs
     * @param void
     * @return void
     * @access public
     */
    public function __construct()
    {

        parent::__construct();

        /* get all the APNS files */
        $this->apnsDir = $_SERVER['DOCUMENT_ROOT'] . '/application/third_party/ApnsPHP/';
        $this->_apns_req();

        return;

    } /* /__construct() */

    // -----------------------------------------------

    /**
     * Will send the actual iOS notification to the user
     * @param $token string iOS device token
     * @param $msg string
     * @param $attrs array Key/value pairs to be sent as meta with APN
     * @return void
     * @access public
     */
    function send_apns($token = null, $msg = null, $attrs = array())
    {
        cutomlogs(APPPATH . 'logs/' . $this->log_file, "APNS DEBUG: APNS START");
        cutomlogs(APPPATH . 'logs/' . $this->log_file, "APNS DEBUG: APNS Token: " . $token);
        cutomlogs(APPPATH . 'logs/' . $this->log_file, "APNS DEBUG: APNS Message: " . $msg);
        if (!$token || !$msg) {
            return;
        }

        // Instantiate a new ApnsPHP_Push object
        $push = new ApnsPHP_Push(
//            ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
//            APNS_CERT_DEV
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            APNS_CERT
        );

        // Set the Provider Certificate passphrase
//        $push->setProviderCertificatePassphrase('12345678');

        // Set the Root Certificate Autority to verify the Apple remote peer
        $push->setRootCertificationAuthority($this->apnsDir . 'SSL/entrust_root_certification_authority.pem');

        // Connect to the Apple Push Notification Service
        $push->connect();

        // Instantiate a new Message with a single recipient
        $message = new ApnsPHP_Message($token);

        // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
        // over a ApnsPHP_Message object retrieved with the getErrors() message.
        $message->setCustomIdentifier("Message-Badge-3");

        // Set badge icon to "3"
        // $message->setBadge(0);

        // Set a simple welcome text
        $message->setText($msg);

        // Play the default sound
        $message->setSound();

        // Set custom properties
        if (is_array($attrs) && count($attrs)) {
            foreach ($attrs as $attr_key => $attr_val) {
                $message->setCustomProperty($attr_key, $attr_val);
            }
        }

        // Set the expiry value - in seconds
        $message->setExpiry(120);

        // Add the message to the message queue
        $push->add($message);

        // Send all messages in the message queue
        $push->send();

        // Disconnect from the Apple Push Notification Service
        $push->disconnect();

        // Examine the error message container
        // $aErrorQueue = $push->getErrors();
        // if (!empty($aErrorQueue)) {
        //  var_dump($aErrorQueue);
        // }

        $aErrorQueue = $push->getErrors();
        if (!empty($aErrorQueue)) {
            cutomlogs(APPPATH . 'logs/' . $this->log_file, "APNS Error: $aErrorQueue");
        }

        cutomlogs(APPPATH . 'logs/' . $this->log_file, "APNS DEBUG: APNS STOP");

        return true;

    } /* /send_ios() */

    function broadcats_apns($msg = null, $attrs = array())
    {
        if (!$msg) {
            return;
        }

        $this->db->select('apns_id');
        $this->db->where('apns_id !=', '');
        $query = $this->db->get('auth_user');

        // Instantiate a new ApnsPHP_Push object
        $push = new ApnsPHP_Push(
//            ApnsPHP_Abstract::ENVIRONMENT_SANDBOX,
//            APNS_CERT_DEV
            ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION,
            APNS_CERT
        );

        // Set the Provider Certificate passphrase
        $push->setProviderCertificatePassphrase('12345678');

        // Set the Root Certificate Autority to verify the Apple remote peer
        $push->setRootCertificationAuthority($this->apnsDir . 'SSL/entrust_root_certification_authority.cer');

        // Connect to the Apple Push Notification Service
        $push->connect();

        foreach ($query->result_array() as $row) {
            // Instantiate a new Message with a single recipient
            $message = new ApnsPHP_Message($row['apns_id']);

            // Set a custom identifier. To get back this identifier use the getCustomIdentifier() method
            // over a ApnsPHP_Message object retrieved with the getErrors() message.
            $message->setCustomIdentifier("Message-Badge-3");

            // Set badge icon to "3"
            // $message->setBadge(0);

            // Set a simple welcome text
            $message->setText($msg);

            // Play the default sound
            $message->setSound();

            // Set custom properties
            if (is_array($attrs) && count($attrs)) {
                foreach ($attrs as $attr_key => $attr_val) {
                    $message->setCustomProperty($attr_key, $attr_val);
                }
            }

            // Set the expiry value - in seconds
            $message->setExpiry(120);

            // Add the message to the message queue
            $push->add($message);
        }

        // Send all messages in the message queue
        $push->send();

        // Disconnect from the Apple Push Notification Service
        $push->disconnect();

        // Examine the error message container
        $aErrorQueue = $push->getErrors();
        if (!empty($aErrorQueue)) {
//             var_dump($aErrorQueue);
            cutomlogs(APPPATH . 'logs/' . $this->log_file, "APNS Error: $aErrorQueue");
        }

        return true;
    }

    // -----------------------------------------------

    private function _apns_req()
    {

        require_once $this->apnsDir . 'Abstract.php';
        require_once $this->apnsDir . 'Exception.php';
        require_once $this->apnsDir . 'Feedback.php';
        require_once $this->apnsDir . 'Message.php';
        require_once $this->apnsDir . 'Log/Interface.php';
        require_once $this->apnsDir . 'Log/Embedded.php';
        require_once $this->apnsDir . 'Message/Custom.php';
        require_once $this->apnsDir . 'Message/Exception.php';
        require_once $this->apnsDir . 'Push.php';
        require_once $this->apnsDir . 'Push/Exception.php';
        require_once $this->apnsDir . 'Push/Server.php';
        require_once $this->apnsDir . 'Push/Server/Exception.php';

        return;

    } /* /_apns_req() */

} /* /Notification_model{} */

/* End of file notification_model.php */
/* Location: ./application/models/notification_model.php */