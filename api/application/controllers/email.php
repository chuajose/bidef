<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email
 *
 * Conexión al buzon imap
 * 
 * Puesdes utlizar el addon https://addons.mozilla.org/es/firefox/addon/restclient/ para las pruebas
 *
 * @package		Imap
 * @subpackage	Email
 * @category	Controller
 * @author		Jose Manuel Súarez Bravo
 * @see         https://github.com/chuajose
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Email extends REST_Controller
{
    var $separador = ".";

    function __construct(){

        parent::__construct();

        $this->load->library('imap');

        $this->load->model('imap_model');

        $this->load->config('imap',TRUE);

        $user = $this->imap_model->get(1);

        $this->login = $this->imap->connect( $user->user, $user->password);//usuario y password
        
        $this->data['error'] = 0;

        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        
    }

    /**
     * Devuelve las bandejas 
     *
     * @return array
     */
    function mailbox_get()
    {
        $bandejas      = $this->imap->get_listing_folders();
        $folder_config = $this->config->item('folders','imap');
        $folders       = array();
        $clients       = array();
        $alums         = array();
        $aytos         = array();
        $others        = array();

        if( !empty( $bandejas )) {
            $i=count($folder_config);
            foreach ($bandejas as $key => $value) {
               
                if($key             =='INBOX') {
                $value['name_show'] = (isset($folder_config[$key])) ? $folder_config[$key] : "Entrada";
                $folders[0]         =$value; 
                }elseif($key        =='Enviados') {
                $value['name_show'] = (isset($folder_config[$key])) ? $folder_config[$key] : "Enviados";
                $folders[1]         =$value; 
                }elseif($key        =='Papelera') {
                $value['name_show'] = (isset($folder_config[$key])) ? $folder_config[$key] : "Papelera";
                $folders[2]         =$value;
                }elseif($key        =='Borradores') {
                $value['name_show'] = (isset($folder_config[$key])) ? $folder_config[$key] : "Borradores";
                $folders[3]         =$value;
                }elseif($key        =='Clientes') {
                $clients[$i]        =$value;
                }elseif($key        =='Ayuntamientos') {
                $aytos[$i]          =$value;
                }elseif($key        =='Alumnos') {
                $alums[$i]          =$value;
                }else $others[]     =$value; 

               $i++;
            }
        }
        ksort($folders);
        
        $this->data['bandejas']               = $folders;
        $this->data['bandejas_clientes']      = $clients;
        $this->data['bandejas_alumnos']       = $alums;
        $this->data['bandejas_ayuntamientos'] = $aytos;
        $this->data['bandejas_otros']         = $others;

       // var_dump($this->data);

        /*if($drafts = $this->imap_model->get_draft(1)) {

            $this->data['borradores'] = $drafts->result();

        } else {

            $this->data['borradores'] = false;

        }*/

        $this->response($this->data, 200);

    }

     /**
     * Actualiza el nombre la las carpetas
     * 
     * @param string $mailbox Nombre de la carpeta que se desea cambiar
     * @param string $mailbox_new Nombre nuevo para la carpeta
     *
     * @return response
     */
    function mailbox_put()
    {
        if( $this->put( 'mailbox' ) && $this->put( 'mailbox_new') ) {   

            $_POST['mailbox']     = $this->put('mailbox');
            
            $_POST['mailbox_new'] = $this->put('mailbox_new');

            $this->form_validation->set_rules('mailbox_new', 'mailbox_new', 'trim|require');

            $this->form_validation->set_rules('mailbox', 'mailbox', 'trim|require');

            if ($this->form_validation->run() === TRUE)
            {

                $mailbox               = $this->put( 'mailbox' );
                
                $mailbox_new           = $this->put( 'mailbox_new' );
                
                $this->data['mailbox'] = $this->imap->rename_mailbox($mailbox, $mailbox_new);


            } else {

                $this->data['error'] = validation_errors();

            }

        } else {

            $this->data['error'] = 1; //No existen datos
        }

        $this->response($this->data, 200);


    }

    /**
     * Borra la carpeta
     * 
     * @param string $mailbox Nombre de la carpeta que se desea borrar
     *
     * @return response
     */
    function mailbox_delete()
    {
        if( $this->delete( 'mailbox' ) ) {
            
            $this->data['mailbox'] = $this->imap->delete_mailbox($this->delete('mailbox'));

        }

        $this->response($this->data, 200);


    }

    /**
     * Crear la carpeta
     * 
     * @param string $mailbox Nombre de la carpeta que se desea crear
     *
     * @return response
     */
    function mailbox_post()
    {

        if( $this->post( 'mailbox' ) ) {

            $this->form_validation->set_rules('mailbox', 'mailbox', 'trim|require');

            if ($this->form_validation->run() === TRUE)
            {

                $mailbox               = $this->post( 'mailbox' );
                                
                if( $this->imap->create_mailbox($mailbox) ) {


                } else {

                    $this->data['msg'] = imap_last_error();
                }

            } else {

                $this->data['error'] = 2;

            }

        } else {

            $this->data['error'] = 1;
        }
        

        $this->response($this->data, 200);


    }
    /**
     * Devuelve los email solicitados
     *
     * @param int $page parametro get con el valor de la pagina a cargar
     * @return array
     */
	function mails_get() {

        if($this->get('page')) {

            $page   = $this->get('page');
            
        } else {

            $page = 1;
        }

        if($this->get('mailbox')) {

            $mailbox = $this->get('mailbox');
            
        } else {

            $mailbox = 'inbox';
        }

        if($mailbox=="borradores") {

            $list=array();
            $emails = $this->imap_model->get_draft(1);

            if($emails) {
                foreach ($emails as $value) {
                   
                   $list[]=array('date'=>$value->date, 'from'=>$value->dest,'subject'=>$value->subject,'uid'=>0);
                }
            }
            $total = count($list);

        } else {

            $this->imap->change_imap_stream($mailbox);
            list($list,$total) = $this->imap->paginate_mails($page);
           // var_dump($list);  
            if($this->get('mailbox')=="INBOX" && !empty($list)){
                $i=0;
                foreach ($list as $value) {
                    $this->load->model('imap_model');
                        //Recorro los mensajes sin leer
                      // if(!$value->seen){
                            preg_match('/<?([-!#$%&\'*+\.\/0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\.\/0-9=?A-Z^_`a-z{|}~]+)>?/',$value->from,$mail_dest);
                            $rules = $this->imap_model->get_rules(0,$mail_dest[1]);
                            if($rules && $rules->destination != $mailbox){
                             //   echo $rules->destination;
                               // $cambio = $this->imap->change_imap_stream($rules->destination);
                               // var_dump($this->imap->scan_mailbox($rules->destination));
                               // 
                            if(!$this->imap->scan_mailbox('Clientes'.$this->seperador.$rules->destination))$this->imap->create_mailbox('Clientes'.$this->seperador.$rules->destination);
                            $this->imap->move_mail($value->uid,'Clientes.'.$rules->destination);
                            unset($list[$i]);
                        //}
                    }
                    $i++;
                }
            }
        }

       
        
        $this->data['emails'] = $list;

        $this->data['total'] = $total;

		$this->response($this->data, 200);
	}

    /**
     * Devuelve los email solicitados
     *
     * @param int $page parametro post con el valor de la pagina a cargar
     * @param array $search parametro post con el indice del nombre del campo a buscar y  el valor de la busqueda a realizar
     * @return array
     */
    function mails_post() {

        if($this->post('page')) {

            $page = $this->post('page');
            
        } else {

            $page = 1;
        }

        $string = '';

        if($this->post('to') && $this->post('to')       !=="") $string .=' FROM "'.$this->post('to').'"';
        if($this->post('useen') && $this->post('useen') =="true") $string .=' UNSEEN '; 
        if($this->post('start') && $this->post('start') !=="") $string .=' SINCE "'.$this->post('start').'"';
        if($this->post('end') && $this->post('end')     !=="") $string .=' BEFORE "'.$this->post('end').'"';
        if($this->post('word')) {

            $criteria[] =  'SUBJECT "'.$this->post('word').'"' .$string;
            $criteria[] =  'BODY "'.$this->post('word').'"' .$string;
            //$criteria[] = array('field' => 'BODY', 'value'=>'casa');
            
        } else {
            if($this->post('subject') && $this->post('subject') !=="") $string .=' SUBJECT "'.$this->post('subject').'"';
            if($this->post('body') && $this->post('body') !=="") $string .=' BODY "'.$this->post('body').'"';
            $criteria[] = $string;
        }


        if($this->post('mailbox'))
            $mailbox = $this->post('mailbox');
        else $mailbox = "INBOX";

        $this->imap->change_imap_stream($mailbox);//Si exsite mailbox selecciono el mail de esa carpeta

        $emails               = $this->imap->search_mails($criteria,$page);
        
        list($list,$total)    = $emails;
        
        $this->data['emails'] = $list;
        
        $this->data['total']  = $total;

        $this->response($this->data, 200);
    }

    /**
     * Devuelve los datos de un email
     * 
     * @param string $id uid del mensaje a cargar
     *
     * @return response
     */
    function mail_get()
    {

        if(!$this->get('id'))
        {
            $this->response($this->data, 400);
        }

        if($this->get('mailbox'))$this->imap->change_imap_stream($this->get('mailbox'));//Si exsite mailbox selecciono el mail de esa carpeta

        $email                  = $this->imap->get_mail($this->get('id')); 
        
        $this->data['header']   = $email; //Datos del email
        
        $this->data['adjuntos'] = $email->get_attachments(); //Datos de los adjuntos

        //Mostramos la vista
        if(!is_null($this->data['header']->textHtml)){

            $this->data['view'] = $email->replace_internal_links(base_url().'adjuntos');
        } else {

            $this->data['view'] = "<pre>".$this->data['header']->textPlain."</pre>";
        }

        $this->response($this->data, 200);

    }

    /**
     * Actualiza los datos de un email
     * 
     * @param int $id uid del mensaje a modificar
     * @param string $action tipo de accion a realizar sobre el mensaje (read, unread, important)
     * @param string $mailbox nombre de la carpeta a la que mover el mensaje. Opcional, solo necestario con la accion move
     * @return response
     */
    function mail_put()
    {

        if($this->put('mailbox'))$this->imap->change_imap_stream($this->put('mailbox'));//Si exsite mailbox selecciono el mail de esa carpeta

        if(!$this->put('id') && !$this->put('action'))
        {
            $this->response($this->data, 400);
        }
        
        if($this->put('action') === "read") {

            if(!$this->imap->mark_mails_as_read($this->put('id'))){
                $this->data['error'] = 2;
            }

        } elseif($this->put('action') === "unread") {

            if(!$this->imap->mark_mails_as_unread($this->put('id'))){
                $this->data['error'] = 2;
            }

        } elseif($this->put('action') === "important") {

            if(!$this->imap->mark_mails_as_important($this->put('id'))){
                $this->data['error'] = 2;
            }

        } elseif ($this->put('action') === "move" && $this->put('mailbox')) {

            if(!$this->imap->move_mail($this->put('id'),$this->put('mailbox'))){
                $this->data['error'] = 3;
            }
            
        }

        $this->response($this->data, 200);
        
    }

    /**
     * Elimina  un email
     * 
     * @param string $id uid del mensaje a eliminar
     *
     * @return response
     */
    function mail_delete()
    {
       // $this->delete('id')= $_GET['id'];

        if(!$this->delete('id'))
        {
            //$this->response($this->data, 400);
        }
        if($this->delete('mailbox'))$this->imap->change_imap_stream($this->delete('mailbox'));//Si exsite mailbox selecciono el mail de esa carpeta

        if(!$this->imap->delete_mail($_GET['id']))
        {
            $this->data['error'] = 2;
        }

        $this->response($this->data, 200);
    }

    /**
     * Envia  un email
     * 
     *
     * @return response
     */
    function mail_post()
    {
        $this->load->model('imap_model');

        if($this->post('borrador')==1) {

            /*$datos = array(

            'dest' => $this->post('to'),
            'subject' => $this->post('subject'),
            'message' => $this->post('message'),
            'fid_usuario' => 1,
            'date' => date('Y-m-d H:i:s')
            );
            if($this->post('draft') && $this->post('draft')>0){
                $draft=$this->imap_model->update_draft($this->post('draft'),$datos);

            } else {
                $draft=$this->imap_model->add_draft($datos);
            }

            $this->data['draft']= $draft;*/

        } else {

            if(is_array($this->post('to'))){

                $to = implode(',', $this->post('to'));
            } else {
                $to = $this->post('to');
            }
        	//echo $this->post('to');die();
           $this->load->library('email');
           
            $this->email->from('bidef@josebravo.es', 'Bidef');
            $this->email->to( $to);

            $this->email->subject( $this->post('subject'));
            $this->email->message( $this->input->post('message'));
            //var_dump($this->post('attachements'));
            $string="";


            $body=array();

            $part1["type"] = TYPEMULTIPART;
            $part1["subtype"] = "mixed";

            $body[]=$part1;

            $attachements = $this->post('attachements');

            if(!empty($attachements)){
                foreach ($this->post('attachements') as $key => $value) {
                     $this->email->attach('adjuntos/1/'.$value);


                    $part2                         =  array();
                    
                    $filename                      = 'adjuntos/1/'.$value;
                    $fp                            = fopen($filename, "r");
                    $contents                      = fread($fp, filesize($filename));
                    fclose($fp);
                    
                    $part2["type"]                 = TYPEAPPLICATION;
                    $part2["encoding"]             = ENCBASE64;
                    $part2["subtype"]              = "octet-stream";
                    $part2["description"]          = $value;
                    $part2['disposition.type']     = 'attachment';
                    $part2['disposition']          = array ('filename' => $value);
                    $part2['type.parameters']      = array('name' => $value);
                    $part2['dparameters.filename'] = $value;
                    $part2["contents.data"]        = base64_encode($contents);
                    $body[]                        =$part2;

                }
            }

            $envio = $this->email->send();

            if($envio){

                $envelope["from"]       = "bidef@josebravo.es";
                $envelope["to"]         = $to;
                //$envelope["cc"]       = "bar@example.com";
                $envelope["subject"]    = $this->post('subject');
                $envelope["date"]       = date('Y-m-d H:i:s');
                
                
                $part3["type"]          = TYPETEXT;
                $part3["subtype"]       = "plain";
                $part3["description"]   = "description";
                $part3["contents.data"] = strip_tags($this->input->post('message'))."\n\n\n\t";
                
                $part4["type"]          = TYPETEXT;
                $part4["subtype"]       = "html";
                $part4["description"]   = "description";
                $part4["contents.data"] = $this->input->post('message')."\n\n\n\t";
                $body[]                 = $part4;
                //$body[] = $part3;

                $mail= imap_mail_compose($envelope, $body);

                $this->imap->move_sent_mail('Sent', $mail);
            }
           // echo $this->email->print_debugger();

        
            
           
        }
        
        $this->response($this->data, 200);
    }

    function attachment_post()
    {
    	//die('asdfsdf');
        $this->load->helper('fichero_helper');

        $fichero = new fichero();
        $fichero->carpeta="adjuntos";
        
        $this->data['file'] = $fichero->subir("todos","uploadfile");
        $this->response($this->data, 200);

    }

    function attachment_delete()
    {
        $this->load->helper('fichero_helper');
        //echo $this->delete('name');
        $fichero = new fichero();
        $fichero->carpeta="adjuntos";
        $fichero->borrar_ficheros($this->delete('name'));

       // $this->data['file']=$_FILES['uploadfile'];
        $this->response($this->data, 200);
    }
}
