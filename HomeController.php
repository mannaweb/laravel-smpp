<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelSmpp\LaravelSmppServiceProvider;
use LaravelSmpp\SmppServiceInterface;
use App\Helpers\Helper;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Log;
use SMPP;
use SmppAddress;
use SmppClient;
use SmppException;
use SocketTransport;
use GsmEncoder;
use SmppTag;
use SmppDeliveryReceipt;

class HomeController extends Controller
{
	
	
   public function __construct(){
        $this->middleware('auth');
        //$this->connect();
      
    }

 
    public function index(){
        return view('home');
    }


    function connect(){

            $transport = new SocketTransport(array('115.112.190.36'), '2776');
            $transport->setRecvTimeout(60000);
            $transport::$forceIpv4 = true;
            $transport->setSendTimeout(60000);

            // Create client
            $smpp = new SmppClient($transport);

            // Activate binary hex-output of server interaction
            $smpp->debug = true;
            $transport->debug = true;

            // Open the connection
            $transport->open();

            // Bind transmitter
            $status = $smpp->bindTransmitter('VIASMPPP', 'demo@123');
            //dd($status);
    }

	
	    public function send(SmppServiceInterface $smpp)
    {
        
$server='';
$port='2776';
$username='';
$password='';
$phone='';
$header='';
$message='fgdfg';

// Construct transport and client
$transport = new SocketTransport(array($server), $port);
$transport->setRecvTimeout(90000);
$transport::$forceIpv4 = true;
$transport->setSendTimeout(90000);

$smpp = new SmppClient($transport);

/*
$class_methods = get_class_methods($smpp);

foreach ($class_methods as $method_name) {
    $d[] =  "$method_name";
}

dd($class_methods);

*/

// Activate binary hex-output of server interaction
$smpp->debug = true;
$transport->debug = true;

// Open the connection
$transport->open();

$smpp->bindTransmitter($username, $password);

SmppClient::$sms_null_terminate_octetstrings = false;
SmppClient::$csms_method = SmppClient::CSMS_PAYLOAD;
SmppClient::$sms_registered_delivery_flag = SMPP::REG_DELIVERY_SMSC_BOTH;

// Prepare message
$encodedMessage = GsmEncoder::utf8_to_gsm0338($message);
$from = new SmppAddress($header, SMPP::TON_ALPHANUMERIC, SMPP::NPI_UNKNOWN);
$to = new SmppAddress($phone, SMPP::TON_INTERNATIONAL, SMPP::NPI_E164);

$tags = array(
            new SmppTag(0x1400, '1101557290000055412'),
            new SmppTag(0x1401, '1107163454194445538')
        );
//$message_id = $smpp->sendSMS($from, $to, $encodedMessage, $tags);

$from->ton = 0;
$from->npi = 0;
//$from->templateId = '';
//$from->template_name = '';
//$from->header = '';

$to->ton = 0;
$to->npi = 0;
//$from->templateId = '';
//$from->template_name = '';
//$from->header = '';

//dd($from, $to, $encodedMessage, $tags);

//print_r($from);

$message_id = $smpp->sendSMS($from, $to, $encodedMessage, $tags);
dd($message_id);die;
$this->keepAlive($smpp);
$status = $this->readSms();
echo '<pre>';print_r($status);


    }


function keepAlive($smpp){
   $smpp->enquireLink();
   $smpp->respondEnquireLink();
}

  
       public function readSms(SmppServiceInterface $smpp)
    {
        $time_start = microtime(true);
        $endtime = $time_start + 120; // 2m
        $lastTime = 0;
        $transport = new SocketTransport(array(config('app.smpp_host')), config('app.smpp_port'));
        $transport->setRecvTimeout(10000); // for this example wait up to 30 seconds for data
        $smpp = new SmppClient($transport);
        // Activate binary hex-output of server interaction
        $smpp->debug = false;
        $transport->debug = false;
        // Open the connection
        $transport->open();
        $smpp->bindReceiver(config('app.smpp_username'), config('app.smpp_password'));
        //$sms = $smpp->readSMS();
        //print_r($sms);die;
        SmppClient::$sms_null_terminate_octetstrings = false;
SmppClient::$csms_method = SmppClient::CSMS_PAYLOAD;
SmppClient::$sms_registered_delivery_flag = SMPP::REG_DELIVERY_SMSC_BOTH;

        $source = new SmppAddress('VIALOG', SMPP::TON_ALPHANUMERIC,SMPP::NPI_UNKNOWN);
      
print_r($source);
        $smpp_id = '051720902001963641716953719240046';

        if (!($smpp_res = $smpp->queryStatus($smpp_id, $source))) {
         $smpp->close();
         throw new Exception('SMPP check error');
     }
    // return $smpp_res;
        print_r($smpp_res);die;
        
    }


   
    
    
	
}
