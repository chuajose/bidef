<?php

/**
 * @see https://github.com/chuajose
 * @author Jose Suarez Bravo
 */
class Imap {
	/**
     * Codificacion del servidor
     *
     * @var string
     **/
	public $server_encoding;

	/**
     * Ruta para adjuntos
     *
     * @var string
     **/
	public $attachments_dir;

	/**
     * Ruta de conexion al servidor
     *
     * @var string
     **/
	public $imap_path;

	/**
     * Registros a cargar en cada pagina
     *
     * @var string
     **/
	public $per_page = 10;
	/**
     * CodeIgniter global
     *
     * @var string
     **/
    protected $ci;

    /**
     * Usuario
     *
     * @var string
     **/
	protected $login;

	/**
     * Contraseña
     *
     * @var string
     **/
	protected $password;

	/**
     * objeto imap
     *
     * @var string
     **/
	protected $imap_stream = NULL;

	/**
     * cuanta imap
     *
     * @var string
     **/
	protected $mailbox = NULL;

	

	public function __construct() {
		$this->ci =& get_instance();
		$this->ci->load->config('imap',TRUE);
		$this->ci->load->model('imap_model');

		$this->server =  $this->ci->config->item('server', 'imap');
		$this->port =  $this->ci->config->item('port', 'imap');
		$this->security =  $this->ci->config->item('security', 'imap');
		$this->cert =  $this->ci->config->item('cert', 'imap');
		$this->server_encoding =  $this->ci->config->item('server_encoding', 'imap');
		$this->attachments_dir =  $this->ci->config->item('attachments_dir', 'imap');
		$this->mailbox = "{" . $this->server . ":" . $this->port . "/" . $this->security . "/" . $this->cert . "}";
		if($this->imap_stream && (!is_resource($this->imap_stream) || !imap_ping($this->imap_stream))) {
				$this->disconnect();
				$this->imap_stream = null;
			}
		

	}

	public function connect($login, $password, $folder = 'inbox')
	{
		$this->folder = $folder;
		$this->imap_path = "{" . $this->server . ":" . $this->port . "/" . $this->security . "/" . $this->cert . "}" . $this->folder;
		$this->login = $login;
		$this->password = $password;
		if($this->attachments_dir) {
			if(!is_dir($this->attachments_dir)) {
				throw new Exception('Directory "' . $this->attachments_dir . '" not found');
			}
			$this->attachments_dir = rtrim(realpath($this->attachments_dir), '\\/');
		}
		
		$this->init_imap_stream();
		if($this->imap_stream) { return true; } else { return false;}

	}

	/**
	 * Selecciona el stream de conexion IMAP
	 * @param bool $forceConnection inicializa la conexion si no esta establecida
	 * @return null|resource
	 */
	protected function init_imap_stream() {
		$this->imap_stream = @imap_open($this->imap_path, $this->login, $this->password);
		if(!$this->imap_stream) {
			throw new Imap_mailbox_exception('Connection error: ' . imap_last_error());
		}

		//Inicializo las tres carpeta necesarias para clasifiacar
		if(!$this->scan_mailbox('Clientes'))$this->create_mailbox('Clientes');
		if(!$this->scan_mailbox('Ayuntamientos'))$this->create_mailbox('Ayuntamientos');
		if(!$this->scan_mailbox('Alumnos'))$this->create_mailbox('Alumnos');

		return true;
	}
	
	protected function disconnect() {
		if($this->imap_stream && is_resource($this->imap_stream)) {
			imap_close($this->imap_stream, CL_EXPUNGE);
		}
	}

	public function change_imap_stream($folder)
	{
		$this->folder = $folder;
		$this->imap_path = "{" . $this->server . ":" . $this->port . "/" . $this->security . "/" . $this->cert . "}" . $this->folder;
		imap_reopen($this->imap_stream, $this->imap_path) or die(implode(", ", imap_errors()));
	}

	/**
	 * Recoje información sobre la bandeja actual
	 *
	 * Devuelve un objeto con informacion con las siguientes propiedates:
	 *  Date - Fecha actual del sistema formateado deacuerdo a RFC2822
	 *  Driver - Protocolo utilzado: POP3, IMAP, NNTP
	 *  Mailbox - El nombre de la bandeja
	 *  Nmsgs - numero de emails en la bajeda
	 *  Recent - numero de emails recientes en la bandeja
	 *
	 * @return stdClass
	 */
	public function check_mailbox() {
		return imap_check($this->imap_stream);
	}

	/**
	 * Crea una nueva bandeja.
	 *
	 * @return bool
	 */

	public function create_mailbox($mailbox) {
		return imap_createmailbox($this->imap_stream, imap_utf7_encode($this->mailbox.$mailbox));
	}

	/**
	 * Elimina la bandeja.
	 *
	 * @param  $mailbox nombre de la carpeta a borrar
	 * @return bool
	 */
	public function delete_mailbox($mailbox)
	{
		return imap_deletemailbox ( $this->imap_stream, $this->mailbox.$mailbox );
	}

	/**
	 * Renombra la bandeja
	 *
	 * @param  $mailbox Nombre de la bandeja de la que queremos cambiar el nombre
	 * @param  $mailbox_new Nombre definitivo de la banejta
	 * 
	 * @return bool
	 */
	public function rename_mailbox($mailbox, $new_mailbox)
	{
		return imap_renamemailbox ( $this->imap_stream, $this->mailbox.$mailbox , $this->mailbox.$new_mailbox );
	}


	public function scan_mailbox($mailbox)
	{
		$folders = imap_list($this->imap_stream, "{".$this->server."}", "*");
		if(!empty($folders))
		{
			foreach ($folders as $folder) {
				$folder_name = str_replace("{".$this->server."}", "", imap_utf7_decode($folder));
				
				if($folder_name==$mailbox) return true;
			}
		}
		
		return false;
	}
	/**
	 * Recoge información sobre la bandeja actual
	 *
	 * Esta funcion devuelve un objeto que conitene la informacion de estado.
	 * Tiene las siguientes propiedades: messages, recent, unseen, uidnext, and uidvalidity.
	 *
	 * @return stdClass | FALSE si la bandeja no existe
	 */

	public function status_mailbox() {
		return imap_status($this->imap_stream, $this->imap_path, SA_ALL);
	}


	

	/**
	 * Recoge el listado de bandejas
	 *
	 * Esta función devuelve un objeto que contiene el listado de bandejas.
	 * El objeto tiene las siguientes propiedades: messages, recent, unseen, uidnext, and uidvalidity.
	 *
	 * @return array listing the folders
	 */

	protected function init_folder($folder,&$array,$data,$key){

		$folders=explode($this->delimiter,$folder);

		if(count($folders)>1){
			$name=array_shift($folders);
			$this->init_folder(implode($this->delimiter,$folders),$array[$name]['folders'],$data,$key);
		}else{

			$sub=array();
			$name=array_shift($folders);
			$array[$name]=array('name'=>$name,'data'=>$data,'folders'=>array());
		}

		return $array;
	}



	public function get_listing_folders() {
		$this->mailboxes = array();

		$folders = imap_getmailboxes($this->imap_stream, "{".$this->server."}", "*");

		foreach ($folders as $key => $folder)
		{
			$this->delimiter = $folder->delimiter;
			$folder = str_replace("{".$this->server."}", "", imap_utf7_decode($folder->name));
		//	$subfolder = explode('.', $folder);
			$this->change_imap_stream($folder);
			//$datos = $this->get_mailbox_info();
			$datos ="";
			$this->init_folder($folder,$this->mailboxes,$datos,$key);

		}

		return $this->mailboxes;
	}


	/**
	 * Esta funcion realiza una busqueda en la bandeja actualmente abierta en el IMAP stream.
	 * Por ejemplo, para buscar todos los mails sin responder enviados por Jose, deberias hacer: "UNANSWERED FROM jose".
	 * La busqueda es sensible a mayúsculas.
	 *
	 * @param string $criteria String, separado por espacion, en el que estan permitidos las siguientes palabra. Si la busqueda es de varias palabras, se deben encerrar entre comillas (e.g. FROM "jose suarez").
	 *    ALL - Retorna todos los mails buscados con el criterio
	 *    ANSWERED - Retorna los mails con la etiqueta \\ANSWERED
	 *    BCC "string" - Retorna los mails con "string" en el campo Bcc:
	 *    BEFORE "date" - mRetorna los mails con faech: anterior "date"
	 *    BODY "string" - Retorna los mails con "string" en el cuerpo del email
	 *    CC "string" - Retorna los mails con "string" en el campo Cc:
	 *    DELETED - Retorna los mails borrados
	 *    FLAGGED - Retorna los mails con la etiqueta \\FLAGGED 
	 *    FROM "string" - Retorna los mails con "string" en el campo From:
	 *    KEYWORD "string" - Retorna los mails con "string" como keyword
	 *    NEW - Retorna los mails nuevos
	 *    OLD - Retorna los mails antiguos
	 *    ON "date" - Retorna los mails con fecha "date"
	 *    RECENT - Retorna los mails con la etiqueta \\RECENT
	 *    SEEN - Retorna los mails leidos (con la etiqueta \\SEEN)
	 *    SINCE "date" - Retorna los mails con fecha despues de "date"
	 *    SUBJECT "string" - Retorna los mails con "string" en el Subject:
	 *    TEXT "string" - Retorna los mails con "string"
	 *    TO "string" - Retorna los mails con "string" en el campo To:
	 *    UNANSWERED - Retorna los mails no respondidos
	 *    UNDELETED - Retorna los mails no borrados
	 *    UNFLAGGED - Retorna los mails sin etiquetas
	 *    UNKEYWORD "string" - Retorna los mails que no tienen keyword "string"
	 *    UNSEEN - Retorna los mails no leidos
	 *
	 * @return array Mails ids
	 */
	public function search_mails($criteria = array(),$page=0) {

		//echo $criteria;
		if(empty($criteria) || $criteria == ""){

			$mailsIds = imap_search($this->imap_stream, 'ALL', SE_UID, $this->server_encoding);

		} else {

			$mailsIds = array();
			foreach ($criteria as $key => $value) {
				$ids = imap_search($this->imap_stream, $value, SE_UID, $this->server_encoding);
				$mailsIds = array_merge($ids,$mailsIds);
			}

		}

		//var_dump($mailsIds);
		if(!$mailsIds) return false;

		$this->total = count($mailsIds);

		$mailsIds = array_chunk($mailsIds, $this->per_page);

		$mailsIds = $mailsIds[$page-1];
		
		//return $mailsIds ? $mailsIds : array();
	    $res = $this->get_mails_info($mailsIds);

	    return array($res,$this->total);

	}

	/**
	 * Devuelve la composicion en formato mime
	 *
	 * Enelope Un array asociativo de campos de cabecers. Las claves válidas son: 
	 * "remail", "return_path", "date", "from", "reply_to", "in_reply_to", "subject", "to", "cc", "bcc", "message_id" y "custom_headers" (que contiene un array asociativo de otras cabeceras). 
	 *
	 * Body Un array asociativo que puede consistir en las siguientes claves: 
	 * "type", "encoding", "charset", "type.parameters", "subtype", "id", "description", "disposition.type", "disposition", "contents.data", "lines", "bytes" y "md5". 
	 * 
	 * @param  $envelope array
	 * @param  $body array
	 * 
	 * @return bool
	 */
	public function create_mail($envelope, $body)
	{
		 return imap_mail_compose ($envelope , $body );
	}

	/**
	 * Envia el email
	 *
	 * @param  $to destinatario del emaill
	 * @param  $subject asunto del email
	 * @param  $message cuerpo del mensaje con formato devuelto  por create_mail
	 * 
	 * @return bool
	 */
	public function send_mail($to, $subject, $message)
	{
		return imap_mail ($to, $subject ,$message);
	}

	/**
	 * Guarda el cuerpo del mensaje.
	 * @return bool
	 */
	public function save_mail($mailId, $filename = 'email.eml') {
		return imap_savebody($this->imap_stream, $filename, $mailId, "", FT_UID);
	}

	/**
	 * Marca el email para el borrado
	 * @return bool
	 */
	public function delete_mail($mailId) {
		return imap_delete($this->imap_stream, $mailId, FT_UID);
	}

	/**
	 * Mueve le correo de bandeja
	 * @return bool
	 */
	public function move_mail($mailId, $mailBox) {

		return imap_mail_move($this->imap_stream, $mailId,$mailBox, CP_UID) && $this->expunge_deleted_mails();
	}

	public function move_sent_mail($mailbox, $mail)
	{
		//Comprobamos si la carpeta enviados ya existe
		$this->get_listing_folders();

		if(!in_array($mailbox, $this->mailboxes)){

		  	$this->create_mailbox($mailbox);

		}

		return imap_append($this->imap_stream,$this->mailbox.$mailbox,$mail,"\\Seen");
	}

	/**
	 * Borra los mensaje marcados para borrar con imap_delete(), imap_mail_move(), o imap_set_flag_full().
	 * @return bool
	 */
	public function expunge_deleted_mails() {
		return imap_expunge($this->imap_stream);
	}

	/**
	 * Añade la etiqueta \Seen a el email.
	 * @return bool
	 */
	public function mark_mail_as_read($mailId) {
		return $this->set_flag(array($mailId), '\\Seen');
	}

	/**
	 * Elimina la etiqueta \Seen en el email.
	 * @return bool
	 */
	public function mark_mail_as_unread($mailId) {
		return $this->clear_flag(array($mailId), '\\Seen');
	}

	/**
	 * Añade la etiqueta \Flagged a el email.
	 * @return bool
	 */
	public function mark_mail_as_important($mailId) {
		return $this->set_flag(array($mailId), '\\Flagged');
	}

	/**
	 * Añade la etiqueta \Seen a el email.
	 * @return bool
	 */
	public function mark_mails_as_read(array $mailId) {
		return $this->set_flag($mailId, '\\Seen');
	}

	/**
	 * Elimina la etiqueta \Seen  de varios emails.
	 * @return bool
	 */
	public function mark_mails_as_unread(array $mailId) {
		return $this->clear_flag($mailId, '\\Seen');
	}

	/**
	 * Añade la etiqueta \Flagged a varios emails.
	 * @return bool
	 */
	public function mark_mails_as_important(array $mailId) {
		return $this->set_flag($mailId, '\\Flagged');
	}

	/**
	 * Añade una etiqueta especifica.
	 *
	 * @param array $mailsIds
	 * @param $flag Las etiquetas pueden ser \Seen, \Answered, \Flagged, \Deleted, and \Draft definidas por RFC2060.
	 * @return bool
	 */
	public function set_flag(array $mailsIds, $flag) {
		return imap_setflag_full($this->imap_stream, implode(',', $mailsIds), $flag, ST_UID);
	}

	/**
	 * Borra etiquetas especificas
	 *
	 * @param array $mailsIds
	 * @param $flag Las etiquetas pueden ser \Seen, \Answered, \Flagged, \Deleted, and \Draft definidas por RFC2060.
	 * @return bool
	 */
	public function clear_flag(array $mailsIds, $flag) {
		return imap_clearflag_full($this->imap_stream, implode(',', $mailsIds), $flag, ST_UID);
	}

	/**
	 * Paginal los resultados de la bandeja referida en el imap_strema
	 *
	 * @param array $page pagina a cargar
	 * @return array que incluye el array de mensajes y el total de mensajes
	 */
	public function paginate_mails($page=1)
	{
		$this->total= $this->count_mails();//cuento

		$mailsIds = $this->sort_mails(SORTARRIVAL,true);

		$mailsIds = array_chunk($mailsIds, $this->per_page);

		$mailsIds = $mailsIds[$page-1];

		$res = $this->get_mails_info($mailsIds);

	    return array($res,$this->total);

	}
	/**
	 * Deveulve las cabeceras de email de la lista de emails
	 *
	 * Devuelve un array de objetos de la cabecera de cada email. El objeto solo define una propiedad si existe. Las posibles propiedades son:
	 *  subject - el asunto del email
	 *  from - quien lo envió
	 *  to - el destinatario
	 *  date - cuando fue enviado
	 *  message_id - Mail-ID
	 *  references - es una referencia al id del mail
	 *  in_reply_to - es una respuesta a la id del email
	 *  size - tamaño del correo
	 *  uid - UID que el email tiene en la bandeja
	 *  msgno - secuencia numerica del email
	 *  recent - si tiene la etiqueta recent
	 *  flagged - si esta etiquetado
	 *  answered - si tiene la etiqueta answered
	 *  deleted - si esta marcado para borrar
	 *  seen - si tiene la etiqueda seen
	 *  draft - si esta etiquetado como draft
	 *
	 * @param array $mailsIds
	 * @return array
	 */
	public function get_mails_info(array $mailsIds) {

		$mails = imap_fetch_overview($this->imap_stream, implode(',', $mailsIds), FT_UID);
		rsort($mails );
		if(is_array($mails) && count($mails))
		{
			foreach($mails as &$mail)
			{
				if(isset($mail->subject)) {
					$mail->subject = $this->decode_mime_str($mail->subject, $this->server_encoding);
				}
				if(isset($mail->from)) {
					$mail->from = $this->decode_mime_str($mail->from, $this->server_encoding);
				}
				if(isset($mail->to)) {
					$mail->to = $this->decode_mime_str($mail->to, $this->server_encoding);
				}

				if(isset($mail->date)){
					$this->ci->load->helper('date');
					$mail->date=strtotime($mail->date,0)*1000;
				}

				if($this->has_attachment($mail->uid)) {
					$mail->attachments = TRUE;
				} else {
					$mail->attachments = FALSE;
				}


			}
		}
		return $mails;
	}

	private function has_attachment($id_msg)
	{
	    
        $adjunto = array();

        $structure = imap_fetchstructure($this->imap_stream,$id_msg, FT_UID);
 
        $attachments = array();

      
        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts))
        {

             //echo "<pre>"; var_dump($structure->parts);echo "</pre>";
            for($i = 0; $i < count($structure->parts); $i++)
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
 
                if($structure->parts[$i]->ifdparameters)
                {
                    foreach($structure->parts[$i]->dparameters as $object)
                    {
                        if(strtolower($object->attribute) == 'filename')
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }
 
                if($structure->parts[$i]->ifparameters)
                {
                    foreach($structure->parts[$i]->parameters as $object)
                    {
                        if(strtolower($object->attribute) == 'name')
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
 
                if($attachments[$i]['is_attachment'])
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($this->imap_stream, $id_msg, $i+1,FT_UID | FT_PEEK);
 
                    /* 4 = QUOTED-PRINTABLE encoding */
                    if($structure->parts[$i]->encoding == 3)
                    {
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 3 = BASE64 encoding */
                    elseif($structure->parts[$i]->encoding == 4)
                    {
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }
        if(!empty($attachments)){

       // echo "<pre>";var_dump($attachments);echo"</pre>";
        /* iterate through each attachment and save it */
            foreach($attachments as $attachment)
            {

                //var_dump($attachment);
                if($attachment['is_attachment'] == 1)
                {
                   
                    $filename = $attachment['name'];
                    if(empty($filename)) $filename = $attachment['filename'];
     
                    if(empty($filename)) $filename = time() . ".dat";
     
                    /* prefix the email number to the filename in case two emails
                     * have the attachment with the same file name.
                     */
                    
                   $adjunto[]=$filename;
                }


     
            }

            
        }   
        if(empty($adjunto)) $adjunto = false;
        return $adjunto;
    }

	/**
	 * Recoge informacion de la bandeja actual.
	 *
	 * Devuelve un objeto con las sigueintes propiedades:
	 *  Date - fecha del ultimo cambio
	 *  Driver - driver
	 *  Mailbox - nombre de la bandeja
	 *  Nmsgs - numero de mensajes
	 *  Recent - numero de mensajes recientes
	 *  Unread - numero de mensajes sin leer
	 *  Deleted - numero de mensajes borrados
	 *  Size - tamaño de la bandeja
	 *
	 * @return object | FALSE si falla
	 */

	public function get_mailbox_info() {
		return imap_mailboxmsginfo($this->imap_stream);
	}

	/**
	 * Ordena los emails en base a un criterio
	 *
	 * El criterio solo puede ser uno. Las constantes son:
	 *  SORTDATE - fecha del email
	 *  SORTARRIVAL - fecha de llegada (default)
	 *  SORTFROM - remitente
	 *  SORTSUBJECT - asunto
	 *  SORTTO - en el campo To 
	 *  SORTCC - en el campo CC
	 *  SORTSIZE - tamaño del email
	 *
	 * @param int $criteria
	 * @param bool $reverse
	 * @return array Mails ids
	 */
	public function sort_mails($criteria = SORTARRIVAL, $reverse = true) {
		return imap_sort($this->imap_stream, $criteria, $reverse, SE_UID);
	}

	/**
	 * Cuenta los mails en una bandeja
	 * @return int
	 */
	public function count_mails() {
		return imap_num_msg($this->imap_stream);
	}

	/**
	 * Devuelve la cuota del usuario
	 * @return array - FALSE en caso de fallo
	 */
	protected function get_quota() {
		return imap_get_quotaroot($this->imap_stream, 'INBOX');
	}

	/**
	 * Devuelve la cuota en KB
	 * @return int - FALSE en caso de fallo
	 */
	public function get_quota_limit() {
		$quota = $this->get_quota();
		if(is_array($quota)) {
			$quota = $quota['STORAGE']['limit'];
		}
		return $quota;
	}

	/**
	 * Devuelve la cuota usada en KB
	 * @return int - FALSE en caso de fallo
	 */
	public function get_quota_usage() {
		$quota = $this->get_quota();
		if(is_array($quota)) {
			$quota = $quota['STORAGE']['usage'];
		}
		return $quota;
	}

	/**
	 * Devuelve los datos del email
	 *
	 * @param $mailId
	 * @return Incoming_mail
	 */
	public function get_mail($mailId) {
		$head = imap_rfc822_parse_headers(imap_fetchheader($this->imap_stream, $mailId, FT_UID));

		$mail = new Incoming_mail();
		$mail->id = $mailId;
		$mail->date = date('Y-m-d H:i:s', isset($head->date) ? strtotime($head->date) : time());
		$mail->subject = isset($head->subject) ? $this->decode_mime_str($head->subject, $this->server_encoding) : null;
		$mail->fromName = isset($head->from[0]->personal) ? $this->decode_mime_str($head->from[0]->personal, $this->server_encoding) : null;
		$mail->fromAddress = strtolower($head->from[0]->mailbox . '@' . $head->from[0]->host);

		if(isset($head->to)) {
			$toStrings = array();
			foreach($head->to as $to) {
				if(!empty($to->mailbox) && !empty($to->host)) {
					$toEmail = strtolower($to->mailbox . '@' . $to->host);
					$toName = isset($to->personal) ? $this->decode_mime_str($to->personal, $this->server_encoding) : null;
					$toStrings[] = $toName ? "$toName <$toEmail>" : $toEmail;
					$mail->to[$toEmail] = $toName;
				}
			}
			$mail->toString = implode(', ', $toStrings);
		}

		if(isset($head->cc)) {
			foreach($head->cc as $cc) {
				$mail->cc[strtolower($cc->mailbox . '@' . $cc->host)] = isset($cc->personal) ? $this->decode_mime_str($cc->personal, $this->server_encoding) : null;
			}
		}

		if(isset($head->reply_to)) {
			foreach($head->reply_to as $replyTo) {
				$mail->replyTo[strtolower($replyTo->mailbox . '@' . $replyTo->host)] = isset($replyTo->personal) ? $this->decode_mime_str($replyTo->personal, $this->server_encoding) : null;
			}
		}

		$mailStructure = imap_fetchstructure($this->imap_stream, $mailId, FT_UID);

		if(empty($mailStructure->parts)) {
			$this->init_mail_part($mail, $mailStructure, 0);
		}
		else {
			foreach($mailStructure->parts as $partNum => $partStructure) {
				$this->init_mail_part($mail, $partStructure, $partNum + 1);
			}
		}

		$mail->replace_internal_links(base_url());
		return $mail;
	}

	protected function init_mail_part(Incoming_mail $mail, $partStructure, $partNum) {
		$data = $partNum ? imap_fetchbody($this->imap_stream, $mail->id, $partNum, FT_UID) : imap_body($this->imap_stream, $mail->id, FT_UID);

		if($partStructure->encoding == 1) {
			$data = imap_utf8($data);
		}
		elseif($partStructure->encoding == 2) {
			$data = imap_binary($data);
		}
		elseif($partStructure->encoding == 3) {
			$data = imap_base64($data);
		}
		elseif($partStructure->encoding == 4) {
			$data = imap_qprint($data);
		}

		$params = array();
		if(!empty($partStructure->parameters)) {
			foreach($partStructure->parameters as $param) {
				$params[strtolower($param->attribute)] = $param->value;
			}
		}
		if(!empty($partStructure->dparameters)) {
			foreach($partStructure->dparameters as $param) {
				$paramName = strtolower(preg_match('~^(.*?)\*~', $param->attribute, $matches) ? $matches[1] : $param->attribute);
				if(isset($params[$paramName])) {
					$params[$paramName] .= $param->value;
				}
				else {
					$params[$paramName] = $param->value;
				}
			}
		}
		if(!empty($params['charset'])) {
			$data = iconv(strtoupper($params['charset']), $this->server_encoding . '//IGNORE', $data);
		}

		// attachments
		$attachmentId = $partStructure->ifid
			? trim($partStructure->id, " <>")
			: (isset($params['filename']) || isset($params['name']) ? mt_rand() . mt_rand() : null);
		if($attachmentId) {
			
			if(empty($params['filename']) && empty($params['name'])) {
				$fileName = $attachmentId . '.' . strtolower($partStructure->subtype);
			}
			else {
				$fileName = !empty($params['filename']) ? $params['filename'] : $params['name'];
				$fileName = $this->decode_mime_str($fileName, $this->server_encoding);
				$fileName = $this->decode_RFC2231($fileName, $this->server_encoding);
			}
			$attachment = new Incoming_mail_attachment();
			$attachment->id = $attachmentId;
			$attachment->name = $fileName;
			if($this->attachments_dir) {
				$replace = array(
					'/\s/' => '_',
					'/[^0-9a-zA-Z_\.]/' => '',
					'/_+/' => '_',
					'/(^_)|(_$)/' => '',
				);
				$fileSysName = preg_replace('~[\\\\/]~', '', $mail->id . '_' . $attachmentId . '_' . preg_replace(array_keys($replace), $replace, $fileName));
				//$this->attachments_dir="http://localhost/php-imap-master/example/attachments";//cambio para que use la de web
				$attachment->filePath = $this->attachments_dir . DIRECTORY_SEPARATOR . $fileSysName;
				//$attachment->size = filesize($this->attachments_dir . DIRECTORY_SEPARATOR . $fileSysName);
				file_put_contents($attachment->filePath, $data);
				$attachment->filePath = base_url()."adjuntos". DIRECTORY_SEPARATOR . $fileSysName;
			}

			$mail->add_attachment($attachment);


		}
		elseif($partStructure->type == 0 && $data) {

			

			if(strtolower($partStructure->subtype) == 'plain') {
				$mail->textPlain .= $data;


			}
			else {
				/*
				 * if data is html checked all links and add target="_blank"
				 */
				libxml_use_internal_errors(true);//user ignore errors  in data
				$dom = new DOMDocument();
				$dom->loadHTML($data);

				$links = array();
				$arr = $dom->getElementsByTagName("a"); // DOMNodeList Object 
				foreach($arr as $item) { 
				    if(!$item->getAttribute('target'))$item->setAttribute('target','_blank');
				}
				$data=$dom->saveHTML();
				/*
				 * fin check html
				 */
				//echo $data;
				$mail->textHtml .= ($data);
			}
		}
		elseif($partStructure->type == 2 && $data) {
			$mail->textPlain .= trim($data);
		}
		if(!empty($partStructure->parts)) {
			//die('si parte type');
			foreach($partStructure->parts as $subPartNum => $subPartStructure) {
				if($partStructure->type == 2 && $partStructure->subtype == 'RFC822') {
					$this->init_mail_part($mail, $subPartStructure, $partNum);
				}
				else {
					$this->init_mail_part($mail, $subPartStructure, $partNum . '.' . ($subPartNum + 1));
				}
			}
		}
	}

	protected function decode_mime_str($string, $charset = 'utf-8') {
		$newString = '';
		$elements = imap_mime_header_decode($string);
		for($i = 0; $i < count($elements); $i++) {
			if($elements[$i]->charset == 'default') {
				$elements[$i]->charset = 'iso-8859-1';
			}
			$newString .= iconv(strtoupper($elements[$i]->charset), $charset . '//IGNORE', $elements[$i]->text);
		}
		return $newString;
	}

	function is_url_encoded($string) {
		$hasInvalidChars = preg_match( '#[^%a-zA-Z0-9\-_\.\+]#', $string );
		$hasEscapedChars = preg_match( '#%[a-zA-Z0-9]{2}#', $string );
		return !$hasInvalidChars && $hasEscapedChars;
	}

	protected function decode_RFC2231($string, $charset = 'utf-8') {
		if(preg_match("/^(.*?)'.*?'(.*?)$/", $string, $matches)) {
			$encoding = $matches[1];
			$data = $matches[2];
			if($this->is_url_encoded($data)) {
				$string = iconv(strtoupper($encoding), $charset . '//IGNORE', urldecode($data));
			}
		}
		return $string;
	}

	public function __destruct() {
		$this->disconnect();
	}
}

class Incoming_mail {

	public $id;
	public $date;
	public $subject;

	public $fromName;
	public $fromAddress;

	public $to = array();
	public $toString;
	public $cc = array();
	public $replyTo = array();

	public $textPlain;
	public $textHtml;
	/** @var Incoming_mail_attachment[] */
	protected $attachments = array();

	public function add_attachment(Incoming_mail_attachment $attachment) {
		$this->attachments[$attachment->id] = $attachment;
	}

	/**
	 * @return Incoming_mail_attachment[]
	 */
	public function get_attachments() {
		return $this->attachments;
	}

	/**
	 * Devuelve un array con linsk internos del HTML
	 * @return array attachmentId => link 
	 */
	public function get_internal_links_placeholders() {
		//return preg_match_all('/=["\'](ci?d:(\w+))["\']/i', $this->textHtml, $matches) ? array_combine($matches[2], $matches[1]) : array();
		return preg_match_all('/=["\'](cid:([\w\.%*@-]+))["\']/i', $this->textHtml, $matches) ? array_combine($matches[2], $matches[1]) : array();
		//return  preg_match_all('/src="cid:(.*)"/Uims', $this->textHtml, $matches)? array_combine($matches[0], $matches[1]) : array();
	}

	public function replace_internal_links($baseUri) {
		$baseUri = rtrim($baseUri, '\\/') . '/';
		$placeholder = base_url();
		$fetchedHtml = $this->textHtml;
		foreach($this->get_internal_links_placeholders() as $attachmentId => $placeholder) {

			//$fetchedHtml = str_replace($attachmentId, " src='". $baseUri . basename($this->attachments[$placeholder]->filePath)."' ", $fetchedHtml);
			//$fetchedHtml = str_replace($placeholder, $baseUri . basename($this->attachments[$attachmentId]->filePath), $fetchedHtml);
			if(isset($this->attachments[$attachmentId]))
                $fetchedHtml = str_replace($placeholder, $baseUri . basename($this->attachments[$attachmentId]->filePath), $fetchedHtml);

		}
		return $fetchedHtml;
	}
}

class Incoming_mail_attachment {

	public $id;
	public $name;
	public $filePath;
	public $size;
}

class Imap_mailbox_exception extends Exception {

}
