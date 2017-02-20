<?php

  error_reporting (7);
  
  //Allowed file extensions
    $file_ext = array('jpg','gif','png','bmp','doc','pdf','ppt','pps','xls'); 
  
  
  
  /**
  
    Note: You can configure here:
    
    1- Send email copies to two additional email addresses.
    
    2- Replace some fields by separator lines to make the email easier to read
    
    3- Setup external email servers and SMTP authentication settings if required
    
    4- Enable or disable the save to Excel action, the email copy and the attachments into the email
    
    Read the comments below for more info.
    
    __________________________________________
    Form2Excel Dreamweaver Extension
    Developed by HotDreamweaver 
    http://www.hotdreamweaver.com
  
  */
  
  // ********************************************************************
  // 1- IF YOU WANT TO SEND COPIES TO ANOTHER EMAIL ADDRESS
  // ********************************************************************
    
  // These emails will receive a copy of the email,
  // set to empty string if you don't want to send some copy.
  // Example:  define('EMAIL_COPY1','my_email@sample.com');
  // Note that the emails has been set to empty string by default (no copies sent).
  define('EMAIL_COPY1','');
  define('EMAIL_COPY2','');
  
  
  // ********************************************************************
  // 2- IF YOU WANT TO PUT SEPARATOR LINES INTO YOUR EMAIL
  // ********************************************************************
    
  // this is the content of the line used as separator
  $separator_item = '##########################';
  
  // These are the names of the separator fields, you can add any number of them, 
  // just add new lines with the desired field's name.
  // The field must be placed as a hidden field into your form in the place that you
  // want to appear the separator line
  $separator_field["sample_field1"] = true;
  $separator_field["sample_secondfield"] = true;
  $separator_field["sample_anotherfield"] = true;
  
  
  // ********************************************************************
  // 3- CONNECTION SETTINGS FOR NON-STANDARD SERVERS
  // ********************************************************************
  
  // If your SMTP server is not the local server, then change it here
  define('SMTP_SERVER', 'localhost');
  
  // If your SMTP server requires authentication then set this parameter to true
  // and put your username and password here
  define('USE_SMTP_AUTHENTICATION', false);
  define('SMTP_USERNAME', 'your_username');
  define('SMTP_password', 'your_password');
  
  
  
  // ********************************************************************
  // 4- ENABLE OR DISABLE EMAIL, EXCEL OR EMAIL'S ATTACHMENTS
  // ********************************************************************  
  
  define("SEND_EMAIL_COPY", true);
  define("SEND_EMAIL_ATTACHMENTS", true);
  define("SAVE_EXCEL_FILE", true);  
  
  
  
  // ********************************************************************
  // MAIN CODE
  // ********************************************************************   
  if (!isset($_GET)) $_GET = $HTTP_GET_VARS;  if (!isset($_POST)) $_POST = $HTTP_POST_VARS; if (!isset($_FILES)) $_FILES = $HTTP_POST_FILES;
  define ("HDW_F2M_UPLOADFOLDER", "hdwuploadfolder");
  define ("HDW_F2M_OK", "hdwok");
  define ("FOLDER_NAME","HDWForm2Excel/saved_forms");

  define ("HDW_F2M_EMAIL", "hdwemail");
  define ("HDW_F2M_NO_OK", "hdwnook");

  foreach ($_FILES as $item => $value)  //extension valid
      if (!isValidExt($value['name']))
      {
          header ("Location: ".getHDWForm2MailParam(HDW_F2M_NO_OK));
          exit;
      }    
            
  function isValidExt($value)
  {
      global $file_ext;
      if ($value!="")
      {
          $pos = strrpos($value,"."); 
          $ext = substr($value,$pos+1);   	
          //echo $ext;
          if(in_array($ext, $file_ext))
          {
              
              return true;
          }    
          else
          {
              
              return false;    
          }    
      } 
      return true;   
  }
  function getHDWForm2ExcelParam($field)
  {
      global $_POST, $_GET; if ($_POST[$field] != "")
          return $_POST[$field];
      else
          return $_GET[$field];
  }

  function getCountryByIp ($ip, $fileid = "1")
  {
      $ip = (function_exists("floatval")?floatval($ip):($ip));
      $ranges = file("iptocountry".$fileid.".csv");
      $start = 0;
      $end = count($ranges) - 1;
      $found = false;
      $country = 0;
      
      while (!$found && $start <= $end) 
      {
         $fpos = floor($start + ($end-$start) / 2);
         $arr = explode (",", $ranges[$fpos]);  $tmpip0 = (function_exists("floatval")?floatval($arr[0]):($arr[0])); $tmpip1 = (function_exists("floatval")?floatval($arr[1]):($arr[1]));         
         if ($tmpip0 <= $ip && $tmpip1 >= $ip)
         {
             $found = true;
             $country = trim($arr[2]);
         }
         else if ($tmpip0 > $ip)
         {
             if ($end == $fpos)
                 $found = true;
             else
                 $end = $fpos;
         }
         else    
         {
             if ($start == $fpos)
             {
                 if ($start < $end)
                     $start = $end;
                 else    
                     $found = true;
             }    
             else          
                 $start = $fpos;
         }    
      }
      
      return $country;
  }  
  
  function getCountryID($ip)
  {
      $cip = IPAddress2IPNumber($ip);
      $country = getCountryByIp($cip);
      if ($country == 0)
          $country = getCountryByIp($cip, "2");    
      return $country;    
  }  
  
  function IPAddress2IPNumber($dotted)
  {
     $dotted = preg_split( "/[.]+/", $dotted);
     $ip = (double) ($dotted[0] * 16777216) + ($dotted[1] * 65536) + ($dotted[2] * 256) + ($dotted[3]);
     return $ip;
  }  
  
  function ckbrowser($user_agent)
  {
 	  if((ereg("Netscape", $user_agent))) $browser = "Netscape";
 	  elseif(ereg("Firefox", $user_agent)) $browser = "Firefox";
      elseif(ereg("Safari", $user_agent)) $browser = "Safari";
      elseif(ereg("SAFARI", $user_agent)) $browser = "SAFARI";
      elseif(ereg("MSIE", $user_agent)) $browser = "MSIE";
      elseif(ereg("Lynx", $user_agent)) $browser = "Lynx";
      elseif(ereg("Opera", $user_agent)) $browser = "Opera";
      elseif(ereg("Gecko", $user_agent)) $browser = "Mozilla";
      elseif(ereg("WebTV", $user_agent)) $browser = "WebTV";
      elseif(ereg("Konqueror", $user_agent)) $browser = "Konqueror";
      else $browser = "bot";
      
      return($browser);
  }
  
  
  function findfilename($path, $name)  
  {
      $pos = strrpos($name,".");    	
      $nameOnly = substr($name,0,$pos);
      $ExtOnly = substr($name,$pos);   	
      $i=0;
      while (file_exists($path.$name))
      {    	     		 
          $name = $nameOnly . "(".$i.")".$ExtOnly; 
    	  $i++;    		 
      }
      $path = str_replace("\\","/",$path);
      return $path.$name;
  }
  
  // country list  
  $countryname["x0"] = 'UNKNOWN';
  $countryname["x1"] = 'AFGHANISTAN';
  $countryname["x2"] = 'ALBANIA';
  $countryname["x3"] = 'ALGERIA';
  $countryname["x4"] = 'AMERICAN SAMOA';
  $countryname["x5"] = 'ANDORRA';
  $countryname["x6"] = 'ANGOLA';
  $countryname["x7"] = 'ANTIGUA AND BARBUDA';
  $countryname["x8"] = 'ARGENTINA';
  $countryname["x9"] = 'ARMENIA';
  $countryname["x10"] = 'AUSTRALIA';
  $countryname["x11"] = 'AUSTRIA';
  $countryname["x12"] = 'AZERBAIJAN';
  $countryname["x13"] = 'BAHAMAS';
  $countryname["x14"] = 'BAHRAIN';
  $countryname["x15"] = 'BANGLADESH';
  $countryname["x16"] = 'BARBADOS';
  $countryname["x17"] = 'BELARUS';
  $countryname["x18"] = 'BELGIUM';
  $countryname["x19"] = 'BELIZE';
  $countryname["x20"] = 'BENIN';
  $countryname["x21"] = 'BERMUDA';
  $countryname["x22"] = 'BHUTAN';
  $countryname["x23"] = 'BOLIVIA';
  $countryname["x24"] = 'BOSNIA AND HERZEGOVINA';
  $countryname["x25"] = 'BOTSWANA';
  $countryname["x26"] = 'BRAZIL';
  $countryname["x27"] = 'BRITISH INDIAN OCEAN TERRITORY';
  $countryname["x28"] = 'BRUNEI DARUSSALAM';
  $countryname["x29"] = 'BULGARIA';
  $countryname["x30"] = 'BURKINA FASO';
  $countryname["x31"] = 'BURUNDI';
  $countryname["x32"] = 'CAMBODIA';
  $countryname["x33"] = 'CAMEROON';
  $countryname["x34"] = 'CANADA';
  $countryname["x35"] = 'CAPE VERDE';
  $countryname["x36"] = 'CAYMAN ISLANDS';
  $countryname["x37"] = 'CENTRAL AFRICAN REPUBLIC';
  $countryname["x38"] = 'CHAD';
  $countryname["x39"] = 'CHILE';
  $countryname["x40"] = 'CHINA';
  $countryname["x41"] = 'COLOMBIA';
  $countryname["x42"] = 'COMOROS';
  $countryname["x43"] = 'CONGO';
  $countryname["x44"] = 'COOK ISLANDS';
  $countryname["x45"] = 'COSTA RICA';
  $countryname["x46"] = 'COTE D\'IVOIRE';
  $countryname["x47"] = 'CROATIA';
  $countryname["x48"] = 'CUBA';
  $countryname["x49"] = 'CYPRUS';
  $countryname["x50"] = 'CZECH REPUBLIC';
  $countryname["x51"] = 'DENMARK';
  $countryname["x52"] = 'DJIBOUTI';
  $countryname["x53"] = 'DOMINICAN REPUBLIC';
  $countryname["x54"] = 'EAST TIMOR';
  $countryname["x55"] = 'ECUADOR';
  $countryname["x56"] = 'EGYPT';
  $countryname["x57"] = 'EL SALVADOR';
  $countryname["x58"] = 'EQUATORIAL GUINEA';
  $countryname["x59"] = 'ERITREA';
  $countryname["x60"] = 'ESTONIA';
  $countryname["x61"] = 'ETHIOPIA';
  $countryname["x62"] = 'FALKLAND ISLANDS (MALVINAS)';
  $countryname["x63"] = 'FAROE ISLANDS';
  $countryname["x64"] = 'FIJI';
  $countryname["x65"] = 'FINLAND';
  $countryname["x66"] = 'FRANCE';
  $countryname["x67"] = 'FRENCH POLYNESIA';
  $countryname["x68"] = 'GABON';
  $countryname["x69"] = 'GAMBIA';
  $countryname["x70"] = 'GEORGIA';
  $countryname["x71"] = 'GERMANY';
  $countryname["x72"] = 'GHANA';
  $countryname["x73"] = 'GIBRALTAR';
  $countryname["x74"] = 'GREECE';
  $countryname["x75"] = 'GREENLAND';
  $countryname["x76"] = 'GRENADA';
  $countryname["x77"] = 'GUADELOUPE';
  $countryname["x78"] = 'GUAM';
  $countryname["x79"] = 'GUATEMALA';
  $countryname["x80"] = 'GUINEA';
  $countryname["x81"] = 'GUINEA-BISSAU';
  $countryname["x82"] = 'HAITI';
  $countryname["x83"] = 'HOLY SEE(VATICAN CITY STATE)';
  $countryname["x84"] = 'HONDURAS';
  $countryname["x85"] = 'HONG KONG';
  $countryname["x86"] = 'HUNGARY';
  $countryname["x87"] = 'ICELAND';
  $countryname["x88"] = 'INDIA';
  $countryname["x89"] = 'INDONESIA';
  $countryname["x90"] = 'IRAQ';
  $countryname["x91"] = 'IRELAND';
  $countryname["x92"] = 'ISLAMIC REPUBLIC OF IRAN';
  $countryname["x93"] = 'ISRAEL';
  $countryname["x94"] = 'ITALY';
  $countryname["x95"] = 'JAMAICA';
  $countryname["x96"] = 'JAPAN';
  $countryname["x97"] = 'JORDAN';
  $countryname["x98"] = 'KAZAKHSTAN';
  $countryname["x99"] = 'KENYA';
  $countryname["x100"] = 'KIRIBATI';
  $countryname["x101"] = 'KUWAIT';
  $countryname["x102"] = 'KYRGYZSTAN';
  $countryname["x103"] = 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC';
  $countryname["x104"] = 'LATVIA';
  $countryname["x105"] = 'LEBANON';
  $countryname["x106"] = 'LESOTHO';
  $countryname["x107"] = 'LIBERIA';
  $countryname["x108"] = 'LIBYAN ARAB JAMAHIRIYA';
  $countryname["x109"] = 'LIECHTENSTEIN';
  $countryname["x110"] = 'LITHUANIA';
  $countryname["x111"] = 'LUXEMBOURG';
  $countryname["x112"] = 'MACAO';
  $countryname["x113"] = 'MADAGASCAR';
  $countryname["x114"] = 'MALAWI';
  $countryname["x115"] = 'MALAYSIA';
  $countryname["x116"] = 'MALDIVES';
  $countryname["x117"] = 'MALI';
  $countryname["x118"] = 'MALTA';
  $countryname["x119"] = 'MARTINIQUE';
  $countryname["x120"] = 'MAURITANIA';
  $countryname["x121"] = 'MAURITIUS';
  $countryname["x122"] = 'MEXICO';
  $countryname["x123"] = 'MONACO';
  $countryname["x124"] = 'MONGOLIA';
  $countryname["x125"] = 'MOROCCO';
  $countryname["x126"] = 'MOZAMBIQUE';
  $countryname["x127"] = 'MYANMAR';
  $countryname["x128"] = 'NAMIBIA';
  $countryname["x129"] = 'NAURU';
  $countryname["x130"] = 'NEPAL';
  $countryname["x131"] = 'NETHERLANDS';
  $countryname["x132"] = 'NETHERLANDS ANTILLES';
  $countryname["x133"] = 'NEW CALEDONIA';
  $countryname["x134"] = 'NEW ZEALAND';
  $countryname["x135"] = 'NICARAGUA';
  $countryname["x136"] = 'NIGER';
  $countryname["x137"] = 'NIGERIA';
  $countryname["x138"] = 'NORTHERN MARIANA ISLANDS';
  $countryname["x139"] = 'NORWAY';
  $countryname["x140"] = 'OMAN';
  $countryname["x141"] = 'PAKISTAN';
  $countryname["x142"] = 'PALAU';
  $countryname["x143"] = 'PALESTINIAN TERRITORY';
  $countryname["x144"] = 'PANAMA';
  $countryname["x145"] = 'PAPUA NEW GUINEA';
  $countryname["x146"] = 'PARAGUAY';
  $countryname["x147"] = 'PERU';
  $countryname["x148"] = 'PHILIPPINES';
  $countryname["x149"] = 'POLAND';
  $countryname["x150"] = 'PORTUGAL';
  $countryname["x151"] = 'PUERTO RICO';
  $countryname["x152"] = 'QATAR';
  $countryname["x153"] = 'REPUBLIC OF KOREA';
  $countryname["x154"] = 'REPUBLIC OF MOLDOVA';
  $countryname["x155"] = 'REUNION';
  $countryname["x156"] = 'ROMANIA';
  $countryname["x157"] = 'RUSSIAN FEDERATION';
  $countryname["x158"] = 'RWANDA';
  $countryname["x159"] = 'SAMOA';
  $countryname["x160"] = 'SAN MARINO';
  $countryname["x161"] = 'SAO TOME AND PRINCIPE';
  $countryname["x162"] = 'SAUDI ARABIA';
  $countryname["x163"] = 'SENEGAL';
  $countryname["x165"] = 'SERBIA AND MONTENEGRO';
  $countryname["x166"] = 'SEYCHELLES';
  $countryname["x167"] = 'SIERRA LEONE';
  $countryname["x168"] = 'SINGAPORE';
  $countryname["x169"] = 'SLOVAKIA';
  $countryname["x170"] = 'SLOVENIA';
  $countryname["x171"] = 'SOLOMON ISLANDS';
  $countryname["x172"] = 'SOMALIA';
  $countryname["x173"] = 'SOUTH AFRICA';
  $countryname["x174"] = 'SPAIN';
  $countryname["x175"] = 'SRI LANKA';
  $countryname["x176"] = 'SUDAN';
  $countryname["x177"] = 'SURINAME';
  $countryname["x178"] = 'SWAZILAND';
  $countryname["x179"] = 'SWEDEN';
  $countryname["x180"] = 'SWITZERLAND';
  $countryname["x181"] = 'SYRIAN ARAB REPUBLIC';
  $countryname["x182"] = 'TAIWAN';
  $countryname["x183"] = 'TAJIKISTAN';
  $countryname["x184"] = 'THAILAND';
  $countryname["x185"] = 'THE DEMOCRATIC REPUBLIC OF THE CONGO';
  $countryname["x186"] = 'THE FORMER YUGOSLAV REPUBLIC OF MACEDONIA';
  $countryname["x187"] = 'TOGO';
  $countryname["x188"] = 'TOKELAU';
  $countryname["x189"] = 'TONGA';
  $countryname["x190"] = 'TRINIDAD AND TOBAGO';
  $countryname["x191"] = 'TUNISIA';
  $countryname["x192"] = 'TURKEY';
  $countryname["x193"] = 'TURKMENISTAN';
  $countryname["x194"] = 'TUVALU';
  $countryname["x195"] = 'UGANDA';
  $countryname["x196"] = 'UKRAINE';
  $countryname["x197"] = 'UNITED ARAB EMIRATES';
  $countryname["x198"] = 'UNITED KINGDOM';
  $countryname["x199"] = 'UNITED REPUBLIC OF TANZANIA';
  $countryname["x200"] = 'UNITED STATES';
  $countryname["x201"] = 'URUGUAY';
  $countryname["x202"] = 'UZBEKISTAN';
  $countryname["x203"] = 'VANUATU';
  $countryname["x204"] = 'VENEZUELA';
  $countryname["x205"] = 'VIET NAM';
  $countryname["x206"] = 'VIRGIN ISLANDS';
  $countryname["x207"] = 'WESTERN SAHARA';
  $countryname["x208"] = 'YEMEN';
  $countryname["x209"] = 'ZAMBIA';
  $countryname["x210"] = 'ZIMBABWE';
  

  if (SAVE_EXCEL_FILE)   
  {
      # To Upload Folder
      $uploadfolder= getHDWForm2ExcelParam(HDW_F2M_UPLOADFOLDER)."_".substr(md5($_SERVER["SERVER_NAME"]),0,4);
     
      $dc = __FILE__;
      if ($dc == '')
          $dc = $_SERVER["SCRIPT_FILENAME"];
      $dc = str_replace("\\","/", $dc);      
      $dc = str_replace("//","/", $dc);
      $dc = substr($dc,0,strrpos($dc, "/")); 
      $documentroot  = $dc;
      define('FIXED',6); 
      define('DC_ROOT',$documentroot."/saved_forms/"); 
      define('DATA_UPLOAD_MAIN',DC_ROOT.$uploadfolder."/");     
      define('DATA_UPLOAD',DC_ROOT.$uploadfolder."/uploads/");         
      define('DATA_FILE',DC_ROOT.$uploadfolder."/data.csv"); 
      
      if (!file_exists(DATA_UPLOAD_MAIN)) 
         if (!(mkdir(DATA_UPLOAD_MAIN)))
         {
             echo "Please, set write permissions to the folder <b>".FOLDER_NAME."</b>.";
             exit;
         }
    
      if (!file_exists(DATA_UPLOAD)) 
      {
          if (!(mkdir(DATA_UPLOAD)))
          {
              echo "Please, set write permissions to the folder <b>".FOLDER_NAME."</b> and items inside.";
              exit;
          }
      }
      
      $formfields = array();
      foreach ($_GET as $item => $value) 
        if ($item != HDW_F2M_OK  && $item != HDW_F2M_UPLOADFOLDER  && $item != FOLDER_NAME && $item != HDW_F2M_EMAIL && $item != HDW_F2M_NO_OK)       
            $formfields[$item] = $value;
      foreach ($_POST as $item => $value)       
        if ($item != HDW_F2M_OK  && $item != HDW_F2M_UPLOADFOLDER  && $item != FOLDER_NAME && $item != HDW_F2M_EMAIL && $item != HDW_F2M_NO_OK)        
            $formfields[$item] = $value;
            
            
      $attachments = Array();      
      foreach ($_FILES as $item => $value)
      {
        if ($value['name']!="")
        {
            $value["filePath"] = findfilename(DATA_UPLOAD, $value['name']);
            move_uploaded_file($value['tmp_name'], $value["filePath"]);
            $formfields[$item] = "http://".$_SERVER["HTTP_HOST"] ."/".FOLDER_NAME."/$uploadfolder/uploads/". basename($value["filePath"]);   
            $attachments[] = Array("file"=>$value['filePath'], "name"=>$value['name'],"content_type"=>$value['type']); 
        }
      }  
      
      
        // 
         
      
      if (file_exists(DATA_FILE))
      {
         $fd = fopen (DATA_FILE, "r");
         $maxsize = filesize(DATA_FILE);
         $headers = fgetcsv($fd,$maxsize);
         $headers2 = array();     
         foreach ($formfields as $item => $value)
         {
            $located = false;
            for ($i=0;$i<(count($headers)-FIXED);$i++)
            {
                if ($item == $headers[$i])
                    $located = true;
            }
            if (!$located)
                $headers2[] = $item;
                
         }
         for ($i=0;$i<(count($headers)-FIXED);$i++)
         {
             if (isset($formfields[$headers[$i]]))
                 $values .= '"'.str_replace('"','""',$formfields[$headers[$i]]).'"'.',';
             else
                 $values .= ',';
             $head .= $headers[$i].',';        
                 
         }   
         if (count($headers2)==0)
         {
            fclose ($fd);
            $fd = fopen (DATA_FILE, "a");         
            $values .= $countryname["x".getCountryID($_SERVER['REMOTE_ADDR'])].",".$_SERVER['REMOTE_ADDR'].",".$_SERVER["HTTP_REFERER"].",".date("Y-m-d H:i:s").",".ckbrowser($_SERVER['HTTP_USER_AGENT']).",".$_SERVER['HTTP_USER_AGENT'].",";
            $values = substr($values,0,(strlen($values)-1))."\n";
            fwrite($fd,($values));
            fclose ($fd); 
            
         } 
         else
         {
            for ($i=0;$i<count($headers2);$i++)
                $ins .= ',';
                
            $contents = "";
            
            while ($v = fgetcsv($fd,$maxsize))
            {
              //if (!feof ($fd))
              {
                  for ($i=0;$i<(count($headers)-FIXED);$i++)
                      $contents .= $v[$i].',';
                  $contents .= $ins;  
                  for ($i=(count($headers)-FIXED);$i<(count($headers));$i++)
                      $contents .= $v[$i].',';
                  $contents = substr($contents,0,(strlen($contents)-1))."\n";   
              }
            }
        
            for ($i=0;$i<count($headers2);$i++)
            {
                $head .= '"'.str_replace('"','""',$headers2[$i]).'"'.',';             
                $values .= '"'.str_replace('"','""',$formfields[$headers2[$i]]).'"'.',';            
            } 
            for ($i=(count($headers)-FIXED);$i<(count($headers));$i++)
                $head .= $headers[$i].',';  
            $values .= $countryname["x".getCountryID($_SERVER['REMOTE_ADDR'])].",".$_SERVER['REMOTE_ADDR'].",".$_SERVER["HTTP_REFERER"].",".date("Y-m-d H:i:s").",".ckbrowser($_SERVER['HTTP_USER_AGENT']).",".$_SERVER['HTTP_USER_AGENT'].",";
            $head = substr($head,0,(strlen($head)-1))."\n";
            $values = substr($values,0,(strlen($values)-1))."\n";
            fclose ($fd);
            $fd = fopen (DATA_FILE, "w"); 
            fwrite($fd,($head.$contents.$values));
            fclose ($fd); 
         }
      }
      else
      {
        $fd = fopen (DATA_FILE, "w");
        foreach ($formfields as $item => $value)
        {
            $head .= '"'.str_replace('"','""',$item).'"'.',';
            $values .= '"'.str_replace('"','""',$value).'"'.',';
        }
        $head .= "Country,IP,Referer,Server Time,Browser,User Agent,";
        $values .= $countryname["x".getCountryID($_SERVER['REMOTE_ADDR'])].",".$_SERVER['REMOTE_ADDR'].",".$_SERVER["HTTP_REFERER"].",".date("Y-m-d H:i:s").",".ckbrowser($_SERVER['HTTP_USER_AGENT']).",".$_SERVER['HTTP_USER_AGENT'].",";
        
        $head = substr($head,0,(strlen($head)-1))."\n";   
        $values = substr($values,0,(strlen($values)-1))."\n";   
        fwrite($fd,($head.$values));
        fclose ($fd);
      }
      
  }  
   
  if (!SEND_EMAIL_COPY)  // if no email copy required then finished here
  {   
      header ("Location: ".getHDWForm2MailParam(HDW_F2M_OK));
      exit;
  } 
   
  //START MAIL CODE
 
  function send_mail($emailaddress, $fromaddress, $emailsubject, $body, $attachments=false)
  {
      $mail = new PHPMailer();
      
      $mail->From     = $fromaddress;
      $mail->AddAddress($emailaddress); 
      
      $mail->WordWrap = 50;                              // set word wrap
      
   
      if ($attachments != false && SEND_EMAIL_ATTACHMENTS)
      {
   

       
       for($i=0; $i < count($attachments); $i++)
       {  
         if ($attachments[$i]["file"] != '')
         {         
           $file_name = $attachments[$i]["name"];
           $mail->AddAttachment($attachments[$i]["file"], $file_name);
         }
       }
      }

    
      $mail->IsHTML(true);                               // send as HTML
      
      $mail->Subject  =  $emailsubject;
      $mail->Body     =  $body;
      $mail->AltBody  =  strip_tags(str_replace("<br />", "\n",str_replace("<br>", "\n", $body)));
      
      $result = $mail->Send();
      
            
      if (!$result)
      {
          $mail->Mailer = "smtp";
          $result = $mail->Send();
      }
      if (!$result)
      {
          $mail->Mailer = "sendmail";
          $result = $mail->Send();
      }
 
      return $result;
  }
  
  function getHDWForm2MailParam($field)
  {
      if ($_POST[$field] != "")
          return $_POST[$field];
      else
          return $_GET[$field];
  }

  
  # To Email Address
  $emailaddress= getHDWForm2MailParam(HDW_F2M_EMAIL);
  $emailaddress = str_replace("+","@", $emailaddress);
  
  # From Email Address
  $fromaddress = $emailaddress;
  
  # Message Subject
  $emailsubject= "Form sent from ".$_SERVER["HTTP_HOST"];
  
  # Use relative paths to the attachments
  if (!SAVE_EXCEL_FILE)
  {
      $attachments = Array();
      foreach ($_FILES as $item => $value)
              $attachments[] = Array("file"=>$value['tmp_name'], "name"=>$value['name'],"content_type"=>$value['type']); 
  }
     
  # Message Body
  $body ="<strong>SUBMITTED INFORMATION</strong><br />"
        ."<strong>***************************</strong><br />";
        
  foreach ($_GET as $item => $value) 
    if ($item != HDW_F2M_OK  && $item != HDW_F2M_UPLOADFOLDER  && $item != FOLDER_NAME && $item != HDW_F2M_EMAIL && $item != HDW_F2M_NO_OK)       
        if ($separator_field[$item])
            $body .= $separator_item."<br /><br />"; 
        else
            $body .="<strong>".$item.":</strong> ".(is_array($value)?htmlentities(implode(", ",$value)):htmlentities($value))."<br /><br />";
  foreach ($_POST as $item => $value)       
    if ($item != HDW_F2M_OK  && $item != HDW_F2M_UPLOADFOLDER  && $item != FOLDER_NAME && $item != HDW_F2M_EMAIL && $item != HDW_F2M_NO_OK)        
        if ($separator_field[$item])
            $body .= $separator_item."<br /><br />"; 
        else
            $body .="<strong>".$item.":</strong> ".(is_array($value)?htmlentities(implode(", ",$value)):htmlentities($value))."<br /><br />"; 
  foreach ($_FILES as $item => $value)
          $body .="<strong>".$item.":</strong> ".$value['name']."<br /><br />"; 
      
  $body .="<strong>SUPPORT INFORMATION</strong><br />"
        ."<strong>***************************</strong><br />"
        ."<strong>Country:</strong> ".$countryname["x".getCountryID($_SERVER['REMOTE_ADDR'])]."<br />"
        ."<strong>IP:</strong> ".$_SERVER['REMOTE_ADDR']."<br />"
        ."<strong>Referer:</strong> ".$_SERVER["HTTP_REFERER"]."<br />"
        ."<strong>Server Time:</strong> ".date("Y-m-d H:i:s")."<br />"
        ."<strong>Browser:</strong> ".ckbrowser($_SERVER['HTTP_USER_AGENT'])."<br />"
        ."<strong>User Agent:</strong> ".$_SERVER['HTTP_USER_AGENT']."<br /><br />"                 
        ."<hr />Delivered by HotDreamweaver Form2Excel Script";
          
  
  // first copy
  if (EMAIL_COPY1 != '')
      send_mail(EMAIL_COPY1, $fromaddress, $emailsubject, $body, $attachments);
  // second copy
  if (EMAIL_COPY2 != '')
      send_mail(EMAIL_COPY2, $fromaddress, $emailsubject, $body, $attachments);
  
  // main email
  if (send_mail($emailaddress, $fromaddress, $emailsubject, $body, $attachments))
     header ("Location: ".getHDWForm2MailParam(HDW_F2M_OK));
     
  else  
      header ("Location: ".getHDWForm2MailParam(HDW_F2M_NO_OK));
  
?>
<?php
////////////////////////////////////////////////////
// PHPMailer - PHP email class
//
// Class for sending email using either
// sendmail, PHP mail(), or SMTP.  Methods are
// based upon the standard AspEmail(tm) classes.
//
// Copyright (C) 2001 - 2003  Brent R. Matzelle
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * PHPMailer - PHP email transport class
 * @package PHPMailer
 * @author Brent R. Matzelle
 * @copyright 2001 - 2003 Brent R. Matzelle
 */
class PHPMailer
{
    /////////////////////////////////////////////////
    // PUBLIC VARIABLES
    /////////////////////////////////////////////////

    /**
     * Email priority (1 = High, 3 = Normal, 5 = low).
     * @var int
     */
    var $Priority          = 3;

    /**
     * Sets the CharSet of the message.
     * @var string
     */
    var $CharSet           = "iso-8859-1";

    /**
     * Sets the Content-type of the message.
     * @var string
     */
    var $ContentType        = "text/plain";

    /**
     * Sets the Encoding of the message. Options for this are "8bit",
     * "7bit", "binary", "base64", and "quoted-printable".
     * @var string
     */
    var $Encoding          = "8bit";

    /**
     * Holds the most recent mailer error message.
     * @var string
     */
    var $ErrorInfo         = "";

    /**
     * Sets the From email address for the message.
     * @var string
     */
    var $From               = "webmaster@audiobionics.com";

    /**
     * Sets the From name of the message.
     * @var string
     */
    var $FromName           = "Webmaster";

    /**
     * Sets the Sender email (Return-Path) of the message.  If not empty,
     * will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
     * @var string
     */
    var $Sender            = "";

    /**
     * Sets the Subject of the message.
     * @var string
     */
    var $Subject           = "";

    /**
     * Sets the Body of the message.  This can be either an HTML or text body.
     * If HTML then run IsHTML(true).
     * @var string
     */
    var $Body               = "";

    /**
     * Sets the text-only body of the message.  This automatically sets the
     * email to multipart/alternative.  This body can be read by mail
     * clients that do not have HTML email capability such as mutt. Clients
     * that can read HTML will view the normal Body.
     * @var string
     */
    var $AltBody           = "";

    /**
     * Sets word wrapping on the body of the message to a given number of 
     * characters.
     * @var int
     */
    var $WordWrap          = 0;

    /**
     * Method to send mail: ("mail", "sendmail", or "smtp").
     * @var string
     */
    var $Mailer            = "mail";

    /**
     * Sets the path of the sendmail program.
     * @var string
     */
    var $Sendmail          = "/usr/sbin/sendmail";
    
    /**
     * Path to PHPMailer plugins.  This is now only useful if the SMTP class 
     * is in a different directory than the PHP include path.  
     * @var string
     */
    var $PluginDir         = "";

    /**
     *  Holds PHPMailer version.
     *  @var string
     */
    var $Version           = "1.73";

    /**
     * Sets the email address that a reading confirmation will be sent.
     * @var string
     */
    var $ConfirmReadingTo  = "";

    /**
     *  Sets the hostname to use in Message-Id and Received headers
     *  and as default HELO string. If empty, the value returned
     *  by SERVER_NAME is used or 'localhost.localdomain'.
     *  @var string
     */
    var $Hostname          = "";

    /////////////////////////////////////////////////
    // SMTP VARIABLES
    /////////////////////////////////////////////////

    /**
     *  Sets the SMTP hosts.  All hosts must be separated by a
     *  semicolon.  You can also specify a different port
     *  for each host by using this format: [hostname:port]
     *  (e.g. "smtp1.example.com:25;smtp2.example.com").
     *  Hosts will be tried in order.
     *  @var string
     */
    var $Host        = "localhost";

    /**
     *  Sets the default SMTP server port.
     *  @var int
     */
    var $Port        = 25;

    /**
     *  Sets the SMTP HELO of the message (Default is $Hostname).
     *  @var string
     */
    var $Helo        = "";

    /**
     *  Sets SMTP authentication. Utilizes the Username and Password variables.
     *  @var bool
     */
    var $SMTPAuth     = false;

    /**
     *  Sets SMTP username.
     *  @var string
     */
    var $Username     = "";

    /**
     *  Sets SMTP password.
     *  @var string
     */
    var $Password     = "";

    /**
     *  Sets the SMTP server timeout in seconds. This function will not 
     *  work with the win32 version.
     *  @var int
     */
    var $Timeout      = 10;

    /**
     *  Sets SMTP class debugging on or off.
     *  @var bool
     */
    var $SMTPDebug    = false;

    /**
     * Prevents the SMTP connection from being closed after each mail 
     * sending.  If this is set to true then to close the connection 
     * requires an explicit call to SmtpClose(). 
     * @var bool
     */
    var $SMTPKeepAlive = false;

    /**#@+
     * @access private
     */
    var $smtp            = NULL;
    var $to              = array();
    var $cc              = array();
    var $bcc             = array();
    var $ReplyTo         = array();
    var $attachment      = array();
    var $CustomHeader    = array();
    var $message_type    = "";
    var $boundary        = array();
    var $language        = array();
    var $error_count     = 0;
    var $LE              = "\n";
    /**#@-*/
    
    /////////////////////////////////////////////////
    // VARIABLE METHODS
    /////////////////////////////////////////////////

    /**
     * Sets message type to HTML.  
     * @param bool $bool
     * @return void
     */
    function IsHTML($bool) {
        if($bool == true)
            $this->ContentType = "text/html";
        else
            $this->ContentType = "text/plain";
    }

    /**
     * Sets Mailer to send message using SMTP.
     * @return void
     */
    function IsSMTP() {
        $this->Mailer = "smtp";
    }

    /**
     * Sets Mailer to send message using PHP mail() function.
     * @return void
     */
    function IsMail() {
        $this->Mailer = "mail";
    }

    /**
     * Sets Mailer to send message using the $Sendmail program.
     * @return void
     */
    function IsSendmail() {
        $this->Mailer = "sendmail";
    }

    /**
     * Sets Mailer to send message using the qmail MTA. 
     * @return void
     */
    function IsQmail() {
        $this->Sendmail = "/var/qmail/bin/sendmail";
        $this->Mailer = "sendmail";
    }


    /////////////////////////////////////////////////
    // RECIPIENT METHODS
    /////////////////////////////////////////////////

    /**
     * Adds a "To" address.  
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddAddress($address, $name = "") {
        $cur = count($this->to);
        $this->to[$cur][0] = trim($address);
        $this->to[$cur][1] = $name;
    }

    /**
     * Adds a "Cc" address. Note: this function works
     * with the SMTP mailer on win32, not with the "mail"
     * mailer.  
     * @param string $address
     * @param string $name
     * @return void
    */
    function AddCC($address, $name = "") {
        $cur = count($this->cc);
        $this->cc[$cur][0] = trim($address);
        $this->cc[$cur][1] = $name;
    }

    /**
     * Adds a "Bcc" address. Note: this function works
     * with the SMTP mailer on win32, not with the "mail"
     * mailer.  
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddBCC($address, $name = "") {
        $cur = count($this->bcc);
        $this->bcc[$cur][0] = trim($address);
        $this->bcc[$cur][1] = $name;
    }

    /**
     * Adds a "Reply-to" address.  
     * @param string $address
     * @param string $name
     * @return void
     */
    function AddReplyTo($address, $name = "") {
        $cur = count($this->ReplyTo);
        $this->ReplyTo[$cur][0] = trim($address);
        $this->ReplyTo[$cur][1] = $name;
    }


    /////////////////////////////////////////////////
    // MAIL SENDING METHODS
    /////////////////////////////////////////////////

    /**
     * Creates message and assigns Mailer. If the message is
     * not sent successfully then it returns false.  Use the ErrorInfo
     * variable to view description of the error.  
     * @return bool
     */
    function Send() {
        $header = "";
        $body = "";
        $result = true;

        if((count($this->to) + count($this->cc) + count($this->bcc)) < 1)
        {
            $this->SetError($this->Lang("provide_address"));
            return false;
        }

        // Set whether the message is multipart/alternative
        if(!empty($this->AltBody))
            $this->ContentType = "multipart/alternative";

        $this->error_count = 0; // reset errors
        $this->SetMessageType();
        $header .= $this->CreateHeader();
        $body = $this->CreateBody();

        if($body == "") { return false; }

        // Choose the mailer
        switch($this->Mailer)
        {
            case "sendmail":
                $result = $this->SendmailSend($header, $body);
                break;
            case "mail":
                $result = $this->MailSend($header, $body);
                break;
            case "smtp":
                $result = $this->SmtpSend($header, $body);
                break;
            default:
            $this->SetError($this->Mailer . $this->Lang("mailer_not_supported"));
                $result = false;
                break;
        }

        return $result;
    }
    
    /**
     * Sends mail using the $Sendmail program.  
     * @access private
     * @return bool
     */
    function SendmailSend($header, $body) {
        if ($this->Sender != "")
            $sendmail = sprintf("%s -oi -f %s -t", $this->Sendmail, $this->Sender);
        else
            $sendmail = sprintf("%s -oi -t", $this->Sendmail);

        if(!@$mail = popen($sendmail, "w"))
        {
            $this->SetError($this->Lang("execute") . $this->Sendmail);
            return false;
        }

        fputs($mail, $header);
        fputs($mail, $body);
        
        $result = pclose($mail) >> 8 & 0xFF;
        if($result != 0)
        {
            $this->SetError($this->Lang("execute") . $this->Sendmail);
            return false;
        }

        return true;
    }

    /**
     * Sends mail using the PHP mail() function.  
     * @access private
     * @return bool
     */
    function MailSend($header, $body) {
        $to = "";
        for($i = 0; $i < count($this->to); $i++)
        {
            if($i != 0) { $to .= ", "; }
            $to .= $this->to[$i][0];
        }

        if ($this->Sender != "" && strlen(ini_get("safe_mode"))< 1)
        {
            $old_from = ini_get("sendmail_from");
            ini_set("sendmail_from", $this->Sender);
            $params = sprintf("-oi -f %s", $this->Sender);
            $rt = @mail($to, $this->EncodeHeader($this->Subject), $body, 
                        $header, $params);
        }
        else
            $rt = @mail($to, $this->EncodeHeader($this->Subject), $body, $header);

        if (isset($old_from))
            ini_set("sendmail_from", $old_from);

        if (!$rt)
           $rt = @mail($to, $this->EncodeHeader($this->Subject), $body, $header); // if not successfull then test without ini_set
                      
       // if(!$rt)
       // {
       //     $this->SetError($this->Lang("instantiate"));
       //     
       //     $rt = $this->SmtpSend($header, $body);  // if not successfull then test smtp
       //     if (!$rt) 
       //        return SendmailSend($header, $body);  // if not successfull then test sendmail
       // }

        return $rt;
    }

    /**
     * Sends mail via SMTP using PhpSMTP (Author:
     * Chris Ryan).  Returns bool.  Returns false if there is a
     * bad MAIL FROM, RCPT, or DATA input.
     * @access private
     * @return bool
     */
    function SmtpSend($header, $body) {
        //include_once($this->PluginDir . "class.smtp.php");
        $error = "";
        $bad_rcpt = array();

        $this->Host = SMTP_SERVER;
        if (USE_SMTP_AUTHENTICATION)
        {
            $this->SMTPAuth = USE_SMTP_AUTHENTICATION;
            $this->Username = SMTP_USERNAME;
            $this->Password = SMTP_PASSWORD;
        }    

        if(!$this->SmtpConnect())
            return false;

        $smtp_from = ($this->Sender == "") ? $this->From : $this->Sender;
        if(!$this->smtp->Mail($smtp_from))
        {
            $error = $this->Lang("from_failed") . $smtp_from;
            $this->SetError($error);
            $this->smtp->Reset();
            return false;
        }

        // Attempt to send attach all recipients
        for($i = 0; $i < count($this->to); $i++)
        {
            if(!$this->smtp->Recipient($this->to[$i][0]))
                $bad_rcpt[] = $this->to[$i][0];
        }
        for($i = 0; $i < count($this->cc); $i++)
        {
            if(!$this->smtp->Recipient($this->cc[$i][0]))
                $bad_rcpt[] = $this->cc[$i][0];
        }
        for($i = 0; $i < count($this->bcc); $i++)
        {
            if(!$this->smtp->Recipient($this->bcc[$i][0]))
                $bad_rcpt[] = $this->bcc[$i][0];
        }

        if(count($bad_rcpt) > 0) // Create error message
        {
            for($i = 0; $i < count($bad_rcpt); $i++)
            {
                if($i != 0) { $error .= ", "; }
                $error .= $bad_rcpt[$i];
            }
            $error = $this->Lang("recipients_failed") . $error;
            $this->SetError($error);
            $this->smtp->Reset();
            return false;
        }

        if(!$this->smtp->Data($header . $body))
        {
            $this->SetError($this->Lang("data_not_accepted"));
            $this->smtp->Reset();
            return false;
        }
        if($this->SMTPKeepAlive == true)
            $this->smtp->Reset();
        else
            $this->SmtpClose();

        return true;
    }

    /**
     * Initiates a connection to an SMTP server.  Returns false if the 
     * operation failed.
     * @access private
     * @return bool
     */
    function SmtpConnect() {
        if($this->smtp == NULL) { $this->smtp = new SMTP(); }

        $this->smtp->do_debug = $this->SMTPDebug;
        $hosts = explode(";", $this->Host);
        $index = 0;
        $connection = ($this->smtp->Connected()); 

        // Retry while there is no connection
        while($index < count($hosts) && $connection == false)
        {
            if(strstr($hosts[$index], ":"))
                list($host, $port) = explode(":", $hosts[$index]);
            else
            {
                $host = $hosts[$index];
                $port = $this->Port;
            }

            if($this->smtp->Connect($host, $port, $this->Timeout))
            {
                if ($this->Helo != '')
                    $this->smtp->Hello($this->Helo);
                else
                    $this->smtp->Hello($this->ServerHostname());
        
                if($this->SMTPAuth)
                {
                    if(!$this->smtp->Authenticate($this->Username, 
                                                  $this->Password))
                    {
                        $this->SetError($this->Lang("authenticate"));
                        $this->smtp->Reset();
                        $connection = false;
                    }
                }
                $connection = true;
            }
            $index++;
        }
        if(!$connection)
            $this->SetError($this->Lang("connect_host"));

        return $connection;
    }

    /**
     * Closes the active SMTP session if one exists.
     * @return void
     */
    function SmtpClose() {
        if($this->smtp != NULL)
        {
            if($this->smtp->Connected())
            {
                $this->smtp->Quit();
                $this->smtp->Close();
            }
        }
    }

    /**
     * Sets the language for all class error messages.  Returns false 
     * if it cannot load the language file.  The default language type
     * is English.
     * @param string $lang_type Type of language (e.g. Portuguese: "br")
     * @param string $lang_path Path to the language file directory
     * @access public
     * @return bool
     */
    function SetLanguage($lang_type, $lang_path = "language/") {
        if(file_exists($lang_path.'phpmailer.lang-'.$lang_type.'.php'))
            include($lang_path.'phpmailer.lang-'.$lang_type.'.php');
        else if(file_exists($lang_path.'phpmailer.lang-en.php'))
            include($lang_path.'phpmailer.lang-en.php');
        else
        {
            $this->SetError("Could not load language file");
            return false;
        }
        $this->language = $PHPMAILER_LANG;
    
        return true;
    }

    /////////////////////////////////////////////////
    // MESSAGE CREATION METHODS
    /////////////////////////////////////////////////

    /**
     * Creates recipient headers.  
     * @access private
     * @return string
     */
    function AddrAppend($type, $addr) {
        $addr_str = $type . ": ";
        $addr_str .= $this->AddrFormat($addr[0]);
        if(count($addr) > 1)
        {
            for($i = 1; $i < count($addr); $i++)
                $addr_str .= ", " . $this->AddrFormat($addr[$i]);
        }
        $addr_str .= $this->LE;

        return $addr_str;
    }
    
    /**
     * Formats an address correctly. 
     * @access private
     * @return string
     */
    function AddrFormat($addr) {
        if(empty($addr[1]))
            $formatted = $addr[0];
        else
        {
            $formatted = $this->EncodeHeader($addr[1], 'phrase') . " <" . 
                         $addr[0] . ">";
        }

        return $formatted;
    }

    /**
     * Wraps message for use with mailers that do not
     * automatically perform wrapping and for quoted-printable.
     * Original written by philippe.  
     * @access private
     * @return string
     */
    function WrapText($message, $length, $qp_mode = false) {
        $soft_break = ($qp_mode) ? sprintf(" =%s", $this->LE) : $this->LE;

        $message = $this->FixEOL($message);
        if (substr($message, -1) == $this->LE)
            $message = substr($message, 0, -1);

        $line = explode($this->LE, $message);
        $message = "";
        for ($i=0 ;$i < count($line); $i++)
        {
          $line_part = explode(" ", $line[$i]);
          $buf = "";
          for ($e = 0; $e<count($line_part); $e++)
          {
              $word = $line_part[$e];
              if ($qp_mode and (strlen($word) > $length))
              {
                $space_left = $length - strlen($buf) - 1;
                if ($e != 0)
                {
                    if ($space_left > 20)
                    {
                        $len = $space_left;
                        if (substr($word, $len - 1, 1) == "=")
                          $len--;
                        elseif (substr($word, $len - 2, 1) == "=")
                          $len -= 2;
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);
                        $buf .= " " . $part;
                        $message .= $buf . sprintf("=%s", $this->LE);
                    }
                    else
                    {
                        $message .= $buf . $soft_break;
                    }
                    $buf = "";
                }
                while (strlen($word) > 0)
                {
                    $len = $length;
                    if (substr($word, $len - 1, 1) == "=")
                        $len--;
                    elseif (substr($word, $len - 2, 1) == "=")
                        $len -= 2;
                    $part = substr($word, 0, $len);
                    $word = substr($word, $len);

                    if (strlen($word) > 0)
                        $message .= $part . sprintf("=%s", $this->LE);
                    else
                        $buf = $part;
                }
              }
              else
              {
                $buf_o = $buf;
                $buf .= ($e == 0) ? $word : (" " . $word); 

                if (strlen($buf) > $length and $buf_o != "")
                {
                    $message .= $buf_o . $soft_break;
                    $buf = $word;
                }
              }
          }
          $message .= $buf . $this->LE;
        }

        return $message;
    }
    
    /**
     * Set the body wrapping.
     * @access private
     * @return void
     */
    function SetWordWrap() {
        if($this->WordWrap < 1)
            return;
            
        switch($this->message_type)
        {
           case "alt":
              // fall through
           case "alt_attachments":
              $this->AltBody = $this->WrapText($this->AltBody, $this->WordWrap);
              break;
           default:
              $this->Body = $this->WrapText($this->Body, $this->WordWrap);
              break;
        }
    }

    /**
     * Assembles message header.  
     * @access private
     * @return string
     */
    function CreateHeader() {
        $result = "";
        
        // Set the boundaries
        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = "b1_" . $uniq_id;
        $this->boundary[2] = "b2_" . $uniq_id;

        $result .= $this->HeaderLine("Date", $this->RFCDate());
        if($this->Sender == "")
            $result .= $this->HeaderLine("Return-Path", trim($this->From));
        else
            $result .= $this->HeaderLine("Return-Path", trim($this->Sender));
        
        // To be created automatically by mail()
        if($this->Mailer != "mail")
        {
            if(count($this->to) > 0)
                $result .= $this->AddrAppend("To", $this->to);
            else if (count($this->cc) == 0)
                $result .= $this->HeaderLine("To", "undisclosed-recipients:;");
            if(count($this->cc) > 0)
                $result .= $this->AddrAppend("Cc", $this->cc);
        }

        $from = array();
        $from[0][0] = trim($this->From);
        $from[0][1] = $this->FromName;
        $result .= $this->AddrAppend("From", $from); 

        // sendmail and mail() extract Bcc from the header before sending
        if((($this->Mailer == "sendmail") || ($this->Mailer == "mail")) && (count($this->bcc) > 0))
            $result .= $this->AddrAppend("Bcc", $this->bcc);

        if(count($this->ReplyTo) > 0)
            $result .= $this->AddrAppend("Reply-to", $this->ReplyTo);

        // mail() sets the subject itself
        if($this->Mailer != "mail")
            $result .= $this->HeaderLine("Subject", $this->EncodeHeader(trim($this->Subject)));

        $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->ServerHostname(), $this->LE);
        $result .= $this->HeaderLine("X-Priority", $this->Priority);
        $result .= $this->HeaderLine("X-Mailer", "PHPMailer [version " . $this->Version . "]");
        
        if($this->ConfirmReadingTo != "")
        {
            $result .= $this->HeaderLine("Disposition-Notification-To", 
                       "<" . trim($this->ConfirmReadingTo) . ">");
        }

        // Add custom headers
        for($index = 0; $index < count($this->CustomHeader); $index++)
        {
            $result .= $this->HeaderLine(trim($this->CustomHeader[$index][0]), 
                       $this->EncodeHeader(trim($this->CustomHeader[$index][1])));
        }
        $result .= $this->HeaderLine("MIME-Version", "1.0");

        switch($this->message_type)
        {
            case "plain":
                $result .= $this->HeaderLine("Content-Transfer-Encoding", $this->Encoding);
                $result .= sprintf("Content-Type: %s; charset=\"%s\"",
                                    $this->ContentType, $this->CharSet);
                break;
            case "attachments":
                // fall through
            case "alt_attachments":
                if($this->InlineImageExists())
                {
                    $result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 
                                    "multipart/related", $this->LE, $this->LE, 
                                    $this->boundary[1], $this->LE);
                }
                else
                {
                    $result .= $this->HeaderLine("Content-Type", "multipart/mixed;");
                    $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                }
                break;
            case "alt":
                $result .= $this->HeaderLine("Content-Type", "multipart/alternative;");
                $result .= $this->TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                break;
        }

        if($this->Mailer != "mail")
            $result .= $this->LE.$this->LE;

        return $result;
    }

    /**
     * Assembles the message body.  Returns an empty string on failure.
     * @access private
     * @return string
     */
    function CreateBody() {
        $result = "";

        $this->SetWordWrap();

        switch($this->message_type)
        {
            case "alt":
                $result .= $this->GetBoundary($this->boundary[1], "", 
                                              "text/plain", "");
                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE.$this->LE;
                $result .= $this->GetBoundary($this->boundary[1], "", 
                                              "text/html", "");
                
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE.$this->LE;
    
                $result .= $this->EndBoundary($this->boundary[1]);
                break;
            case "plain":
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                break;
            case "attachments":
                $result .= $this->GetBoundary($this->boundary[1], "", "", "");
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE;
     
                $result .= $this->AttachAll();
                break;
            case "alt_attachments":
                $result .= sprintf("--%s%s", $this->boundary[1], $this->LE);
                $result .= sprintf("Content-Type: %s;%s" .
                                   "\tboundary=\"%s\"%s",
                                   "multipart/alternative", $this->LE, 
                                   $this->boundary[2], $this->LE.$this->LE);
    
                // Create text body
                $result .= $this->GetBoundary($this->boundary[2], "", 
                                              "text/plain", "") . $this->LE;

                $result .= $this->EncodeString($this->AltBody, $this->Encoding);
                $result .= $this->LE.$this->LE;
    
                // Create the HTML body
                $result .= $this->GetBoundary($this->boundary[2], "", 
                                              "text/html", "") . $this->LE;
    
                $result .= $this->EncodeString($this->Body, $this->Encoding);
                $result .= $this->LE.$this->LE;

                $result .= $this->EndBoundary($this->boundary[2]);
                
                $result .= $this->AttachAll();
                break;
        }
        if($this->IsError())
            $result = "";

        return $result;
    }

    /**
     * Returns the start of a message boundary.
     * @access private
     */
    function GetBoundary($boundary, $charSet, $contentType, $encoding) {
        $result = "";
        if($charSet == "") { $charSet = $this->CharSet; }
        if($contentType == "") { $contentType = $this->ContentType; }
        if($encoding == "") { $encoding = $this->Encoding; }

        $result .= $this->TextLine("--" . $boundary);
        $result .= sprintf("Content-Type: %s; charset = \"%s\"", 
                            $contentType, $charSet);
        $result .= $this->LE;
        $result .= $this->HeaderLine("Content-Transfer-Encoding", $encoding);
        $result .= $this->LE;
       
        return $result;
    }
    
    /**
     * Returns the end of a message boundary.
     * @access private
     */
    function EndBoundary($boundary) {
        return $this->LE . "--" . $boundary . "--" . $this->LE; 
    }
    
    /**
     * Sets the message type.
     * @access private
     * @return void
     */
    function SetMessageType() {
        if(count($this->attachment) < 1 && strlen($this->AltBody) < 1)
            $this->message_type = "plain";
        else
        {
            if(count($this->attachment) > 0)
                $this->message_type = "attachments";
            if(strlen($this->AltBody) > 0 && count($this->attachment) < 1)
                $this->message_type = "alt";
            if(strlen($this->AltBody) > 0 && count($this->attachment) > 0)
                $this->message_type = "alt_attachments";
        }
    }

    /**
     * Returns a formatted header line.
     * @access private
     * @return string
     */
    function HeaderLine($name, $value) {
        return $name . ": " . $value . $this->LE;
    }

    /**
     * Returns a formatted mail line.
     * @access private
     * @return string
     */
    function TextLine($value) {
        return $value . $this->LE;
    }

    /////////////////////////////////////////////////
    // ATTACHMENT METHODS
    /////////////////////////////////////////////////

    /**
     * Adds an attachment from a path on the filesystem.
     * Returns false if the file could not be found
     * or accessed.
     * @param string $path Path to the attachment.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return bool
     */
    function AddAttachment($path, $name = "", $encoding = "base64", 
                           $type = "application/octet-stream") {
       // if(!@is_file($path))
       // {
       //     $this->SetError($this->Lang("file_access") . $path);
       //     return false;
       // }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;

        return true;
    }

    /**
     * Attaches all fs, string, and binary attachments to the message.
     * Returns an empty string on failure.
     * @access private
     * @return string
     */
    function AttachAll() {
        // Return text of body
        $mime = array();

        // Add all attachments
        for($i = 0; $i < count($this->attachment); $i++)
        {
            // Check for string attachment
            $bString = $this->attachment[$i][5];
            if ($bString)
                $string = $this->attachment[$i][0];
            else
                $path = $this->attachment[$i][0];

            $filename    = $this->attachment[$i][1];
            $name        = $this->attachment[$i][2];
            $encoding    = $this->attachment[$i][3];
            $type        = $this->attachment[$i][4];
            $disposition = $this->attachment[$i][6];
            $cid         = $this->attachment[$i][7];
            
            $mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
            $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
            $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

            if($disposition == "inline")
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);

            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", 
                              $disposition, $name, $this->LE.$this->LE);

            // Encode as string attachment
            if($bString)
            {
                $mime[] = $this->EncodeString($string, $encoding);
                if($this->IsError()) { return ""; }
                $mime[] = $this->LE.$this->LE;
            }
            else
            {
                $mime[] = $this->EncodeFile($path, $encoding);                
                if($this->IsError()) { return ""; }
                $mime[] = $this->LE.$this->LE;
            }
        }

        $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);

        return join("", $mime);
    }
    
    /**
     * Encodes attachment in requested format.  Returns an
     * empty string on failure.
     * @access private
     * @return string
     */
    function EncodeFile ($path, $encoding = "base64") {
        if(!@$fd = fopen($path, "rb"))
        {
            $this->SetError($this->Lang("file_open") . $path);
            return "";
        }
        $magic_quotes = get_magic_quotes_runtime();
        set_magic_quotes_runtime(0);
        $file_buffer = fread($fd, filesize($path));
        $file_buffer = $this->EncodeString($file_buffer, $encoding);
        fclose($fd);
        set_magic_quotes_runtime($magic_quotes);

        return $file_buffer;
    }

    /**
     * Encodes string to requested format. Returns an
     * empty string on failure.
     * @access private
     * @return string
     */
    function EncodeString ($str, $encoding = "base64") {
        $encoded = "";
        switch(strtolower($encoding)) {
          case "base64":
              // chunk_split is found in PHP >= 3.0.6
              $encoded = chunk_split(base64_encode($str), 76, $this->LE);
              break;
          case "7bit":
          case "8bit":
              $encoded = $this->FixEOL($str);
              if (substr($encoded, -(strlen($this->LE))) != $this->LE)
                $encoded .= $this->LE;
              break;
          case "binary":
              $encoded = $str;
              break;
          case "quoted-printable":
              $encoded = $this->EncodeQP($str);
              break;
          default:
              $this->SetError($this->Lang("encoding") . $encoding);
              break;
        }
        return $encoded;
    }

    /**
     * Encode a header string to best of Q, B, quoted or none.  
     * @access private
     * @return string
     */
    function EncodeHeader ($str, $position = 'text') {
      $x = 0;
      
      switch (strtolower($position)) {
        case 'phrase':
          if (!preg_match('/[\200-\377]/', $str)) {
            // Can't use addslashes as we don't know what value has magic_quotes_sybase.
            $encoded = addcslashes($str, "\0..\37\177\\\"");

            if (($str == $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str))
              return ($encoded);
            else
              return ("\"$encoded\"");
          }
          $x = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
          break;
        case 'comment':
          $x = preg_match_all('/[()"]/', $str, $matches);
          // Fall-through
        case 'text':
        default:
          $x += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
          break;
      }

      if ($x == 0)
        return ($str);

      $maxlen = 75 - 7 - strlen($this->CharSet);
      // Try to select the encoding which should produce the shortest output
      if (strlen($str)/3 < $x) {
        $encoding = 'B';
        $encoded = base64_encode($str);
        $maxlen -= $maxlen % 4;
        $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
      } else {
        $encoding = 'Q';
        $encoded = $this->EncodeQ($str, $position);
        $encoded = $this->WrapText($encoded, $maxlen, true);
        $encoded = str_replace("=".$this->LE, "\n", trim($encoded));
      }

      $encoded = preg_replace('/^(.*)$/m', " =?".$this->CharSet."?$encoding?\\1?=", $encoded);
      $encoded = trim(str_replace("\n", $this->LE, $encoded));
      
      return $encoded;
    }
    
    /**
     * Encode string to quoted-printable.  
     * @access private
     * @return string
     */
    function EncodeQP ($str) {
        $encoded = $this->FixEOL($str);
        if (substr($encoded, -(strlen($this->LE))) != $this->LE)
            $encoded .= $this->LE;

        // Replace every high ascii, control and = characters
        $encoded = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e',
                  "'='.sprintf('%02X', ord('\\1'))", $encoded);
        // Replace every spaces and tabs when it's the last character on a line
        $encoded = preg_replace("/([\011\040])".$this->LE."/e",
                  "'='.sprintf('%02X', ord('\\1')).'".$this->LE."'", $encoded);

        // Maximum line length of 76 characters before CRLF (74 + space + '=')
        $encoded = $this->WrapText($encoded, 74, true);

        return $encoded;
    }

    /**
     * Encode string to q encoding.  
     * @access private
     * @return string
     */
    function EncodeQ ($str, $position = "text") {
        // There should not be any EOL in the string
        $encoded = preg_replace("[\r\n]", "", $str);

        switch (strtolower($position)) {
          case "phrase":
            $encoded = preg_replace("/([^A-Za-z0-9!*+\/ -])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
            break;
          case "comment":
            $encoded = preg_replace("/([\(\)\"])/e", "'='.sprintf('%02X', ord('\\1'))", $encoded);
          case "text":
          default:
            // Replace every high ascii, control =, ? and _ characters
            $encoded = preg_replace('/([\000-\011\013\014\016-\037\075\077\137\177-\377])/e',
                  "'='.sprintf('%02X', ord('\\1'))", $encoded);
            break;
        }
        
        // Replace every spaces to _ (more readable than =20)
        $encoded = str_replace(" ", "_", $encoded);

        return $encoded;
    }

    /**
     * Adds a string or binary attachment (non-filesystem) to the list.
     * This method can be used to attach ascii or binary data,
     * such as a BLOB record from a database.
     * @param string $string String attachment data.
     * @param string $filename Name of the attachment.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return void
     */
    function AddStringAttachment($string, $filename, $encoding = "base64", 
                                 $type = "application/octet-stream") {
        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $string;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $filename;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = true; // isString
        $this->attachment[$cur][6] = "attachment";
        $this->attachment[$cur][7] = 0;
    }
    
    /**
     * Adds an embedded attachment.  This can include images, sounds, and 
     * just about any other document.  Make sure to set the $type to an 
     * image type.  For JPEG images use "image/jpeg" and for GIF images 
     * use "image/gif".
     * @param string $path Path to the attachment.
     * @param string $cid Content ID of the attachment.  Use this to identify 
     *        the Id for accessing the image in an HTML form.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.  
     * @return bool
     */
    function AddEmbeddedImage($path, $cid, $name = "", $encoding = "base64", 
                              $type = "application/octet-stream") {
    
        if(!@is_file($path))
        {
            $this->SetError($this->Lang("file_access") . $path);
            return false;
        }

        $filename = basename($path);
        if($name == "")
            $name = $filename;

        // Append to $attachment array
        $cur = count($this->attachment);
        $this->attachment[$cur][0] = $path;
        $this->attachment[$cur][1] = $filename;
        $this->attachment[$cur][2] = $name;
        $this->attachment[$cur][3] = $encoding;
        $this->attachment[$cur][4] = $type;
        $this->attachment[$cur][5] = false; // isStringAttachment
        $this->attachment[$cur][6] = "inline";
        $this->attachment[$cur][7] = $cid;
    
        return true;
    }
    
    /**
     * Returns true if an inline attachment is present.
     * @access private
     * @return bool
     */
    function InlineImageExists() {
        $result = false;
        for($i = 0; $i < count($this->attachment); $i++)
        {
            if($this->attachment[$i][6] == "inline")
            {
                $result = true;
                break;
            }
        }
        
        return $result;
    }

    /////////////////////////////////////////////////
    // MESSAGE RESET METHODS
    /////////////////////////////////////////////////

    /**
     * Clears all recipients assigned in the TO array.  Returns void.
     * @return void
     */
    function ClearAddresses() {
        $this->to = array();
    }

    /**
     * Clears all recipients assigned in the CC array.  Returns void.
     * @return void
     */
    function ClearCCs() {
        $this->cc = array();
    }

    /**
     * Clears all recipients assigned in the BCC array.  Returns void.
     * @return void
     */
    function ClearBCCs() {
        $this->bcc = array();
    }

    /**
     * Clears all recipients assigned in the ReplyTo array.  Returns void.
     * @return void
     */
    function ClearReplyTos() {
        $this->ReplyTo = array();
    }

    /**
     * Clears all recipients assigned in the TO, CC and BCC
     * array.  Returns void.
     * @return void
     */
    function ClearAllRecipients() {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
    }

    /**
     * Clears all previously set filesystem, string, and binary
     * attachments.  Returns void.
     * @return void
     */
    function ClearAttachments() {
        $this->attachment = array();
    }

    /**
     * Clears all custom headers.  Returns void.
     * @return void
     */
    function ClearCustomHeaders() {
        $this->CustomHeader = array();
    }


    /////////////////////////////////////////////////
    // MISCELLANEOUS METHODS
    /////////////////////////////////////////////////

    /**
     * Adds the error message to the error container.
     * Returns void.
     * @access private
     * @return void
     */
    function SetError($msg) {
        $this->error_count++;
        $this->ErrorInfo = $msg;
    }

    /**
     * Returns the proper RFC 822 formatted date. 
     * @access private
     * @return string
     */
    function RFCDate() {
        $tz = date("Z");
        $tzs = ($tz < 0) ? "-" : "+";
        $tz = abs($tz);
        $tz = ($tz/3600)*100 + ($tz%3600)/60;
        $result = sprintf("%s %s%04d", date("D, j M Y H:i:s"), $tzs, $tz);

        return $result;
    }
    
    /**
     * Returns the appropriate server variable.  Should work with both 
     * PHP 4.1.0+ as well as older versions.  Returns an empty string 
     * if nothing is found.
     * @access private
     * @return mixed
     */
    function ServerVar($varName) {
        global $HTTP_SERVER_VARS;
        global $HTTP_ENV_VARS;

        if(!isset($_SERVER))
        {
            $_SERVER = $HTTP_SERVER_VARS;
            if(!isset($_SERVER["REMOTE_ADDR"]))
                $_SERVER = $HTTP_ENV_VARS; // must be Apache
        }
        
        if(isset($_SERVER[$varName]))
            return $_SERVER[$varName];
        else
            return "";
    }

    /**
     * Returns the server hostname or 'localhost.localdomain' if unknown.
     * @access private
     * @return string
     */
    function ServerHostname() {
        if ($this->Hostname != "")
            $result = $this->Hostname;
        elseif ($this->ServerVar('SERVER_NAME') != "")
            $result = $this->ServerVar('SERVER_NAME');
        else
            $result = "localhost.localdomain";

        return $result;
    }

    /**
     * Returns a message in the appropriate language.
     * @access private
     * @return string
     */
    function Lang($key) {
        if(count($this->language) < 1)
            $this->SetLanguage("en"); // set the default language
    
        if(isset($this->language[$key]))
            return $this->language[$key];
        else
            return "Language string failed to load: " . $key;
    }
    
    /**
     * Returns true if an error occurred.
     * @return bool
     */
    function IsError() {
        return ($this->error_count > 0);
    }

    /**
     * Changes every end of line from CR or LF to CRLF.  
     * @access private
     * @return string
     */
    function FixEOL($str) {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);
        $str = str_replace("\n", $this->LE, $str);
        return $str;
    }

    /**
     * Adds a custom header. 
     * @return void
     */
    function AddCustomHeader($custom_header) {
        $this->CustomHeader[] = explode(":", $custom_header, 2);
    }
}

?>
<?php
////////////////////////////////////////////////////
// SMTP - PHP SMTP class
//
// Version 1.02
//
// Define an SMTP class that can be used to connect
// and communicate with any SMTP server. It implements
// all the SMTP functions defined in RFC821 except TURN.
//
// Author: Chris Ryan
//

// License: LGPL, see LICENSE
////////////////////////////////////////////////////

/**
 * SMTP is rfc 821 compliant and implements all the rfc 821 SMTP
 * commands except TURN which will always return a not implemented
 * error. SMTP also provides some utility methods for sending mail
 * to an SMTP server.
 * @package PHPMailer
 * @author Chris Ryan
 */
class SMTP
{
    /**
     *  SMTP server port
     *  @var int
     */
    var $SMTP_PORT = 25;
    
    /**
     *  SMTP reply line ending
     *  @var string
     */
    var $CRLF = "\r\n";
    
    /**
     *  Sets whether debugging is turned on
     *  @var bool
     */
    var $do_debug;       # the level of debug to perform

    /**#@+
     * @access private
     */
    var $smtp_conn;      # the socket to the server
    var $error;          # error if any on the last call
    var $helo_rply;      # the reply the server sent to us for HELO
    /**#@-*/

    /**
     * Initialize the class so that the data is in a known state.
     * @access public
     * @return void
     */
    function SMTP() {
        $this->smtp_conn = 0;
        $this->error = null;
        $this->helo_rply = null;

        $this->do_debug = 0;
    }

    /*************************************************************
     *                    CONNECTION FUNCTIONS                  *
     ***********************************************************/

    /**
     * Connect to the server specified on the port specified.
     * If the port is not specified use the default SMTP_PORT.
     * If tval is specified then a connection will try and be
     * established with the server for that number of seconds.
     * If tval is not specified the default is 30 seconds to
     * try on the connection.
     *
     * SMTP CODE SUCCESS: 220
     * SMTP CODE FAILURE: 421
     * @access public
     * @return bool
     */
    function Connect($host,$port=0,$tval=30) {
        # set the error val to null so there is no confusion
        $this->error = null;

        # make sure we are __not__ connected
        if($this->connected()) {
            # ok we are connected! what should we do?
            # for now we will just give an error saying we
            # are already connected
            $this->error =
                array("error" => "Already connected to a server");
            return false;
        }

        if(empty($port)) {
            $port = $this->SMTP_PORT;
        }

        #connect to the smtp server
        $this->smtp_conn = fsockopen($host,    # the host of the server
                                     $port,    # the port to use
                                     $errno,   # error number if any
                                     $errstr,  # error message if any
                                     $tval);   # give up after ? secs
        # verify we connected properly
        if(empty($this->smtp_conn)) {
            $this->error = array("error" => "Failed to connect to server",
                                 "errno" => $errno,
                                 "errstr" => $errstr);
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": $errstr ($errno)" . $this->CRLF;
            }
            return false;
        }

        # sometimes the SMTP server takes a little longer to respond
        # so we will give it a longer timeout for the first read
        // Windows still does not have support for this timeout function
        if(substr(PHP_OS, 0, 3) != "WIN")
           socket_set_timeout($this->smtp_conn, $tval, 0);

        # get any announcement stuff
        $announce = $this->get_lines();

        # set the timeout  of any socket functions at 1/10 of a second
        //if(function_exists("socket_set_timeout"))
        //   socket_set_timeout($this->smtp_conn, 0, 100000);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $announce;
        }

        return true;
    }

    /**
     * Performs SMTP authentication.  Must be run after running the
     * Hello() method.  Returns true if successfully authenticated.
     * @access public
     * @return bool
     */
    function Authenticate($username, $password) {
        // Start authentication
        fputs($this->smtp_conn,"AUTH LOGIN" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 334) {
            $this->error =
                array("error" => "AUTH not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Send encoded username
        fputs($this->smtp_conn, base64_encode($username) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 334) {
            $this->error =
                array("error" => "Username not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        // Send encoded password
        fputs($this->smtp_conn, base64_encode($password) . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($code != 235) {
            $this->error =
                array("error" => "Password not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    /**
     * Returns true if connected to a server otherwise false
     * @access private
     * @return bool
     */
    function Connected() {
        if(!empty($this->smtp_conn)) {
            $sock_status = socket_get_status($this->smtp_conn);
            if($sock_status["eof"]) {
                # hmm this is an odd situation... the socket is
                # valid but we aren't connected anymore
                if($this->do_debug >= 1) {
                    echo "SMTP -> NOTICE:" . $this->CRLF .
                         "EOF caught while checking if connected";
                }
                $this->Close();
                return false;
            }
            return true; # everything looks good
        }
        return false;
    }

    /**
     * Closes the socket and cleans up the state of the class.
     * It is not considered good to use this function without
     * first trying to use QUIT.
     * @access public
     * @return void
     */
    function Close() {
        $this->error = null; # so there is no confusion
        $this->helo_rply = null;
        if(!empty($this->smtp_conn)) {
            # close the connection and cleanup
            fclose($this->smtp_conn);
            $this->smtp_conn = 0;
        }
    }


    /***************************************************************
     *                        SMTP COMMANDS                       *
     *************************************************************/

    /**
     * Issues a data command and sends the msg_data to the server
     * finializing the mail transaction. $msg_data is the message
     * that is to be send with the headers. Each header needs to be
     * on a single line followed by a <CRLF> with the message headers
     * and the message body being seperated by and additional <CRLF>.
     *
     * Implements rfc 821: DATA <CRLF>
     *
     * SMTP CODE INTERMEDIATE: 354
     *     [data]
     *     <CRLF>.<CRLF>
     *     SMTP CODE SUCCESS: 250
     *     SMTP CODE FAILURE: 552,554,451,452
     * SMTP CODE FAILURE: 451,554
     * SMTP CODE ERROR  : 500,501,503,421
     * @access public
     * @return bool
     */
    function Data($msg_data) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Data() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"DATA" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 354) {
            $this->error =
                array("error" => "DATA command not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        # the server is ready to accept data!
        # according to rfc 821 we should not send more than 1000
        # including the CRLF
        # characters on a single line so we will break the data up
        # into lines by \r and/or \n then if needed we will break
        # each of those into smaller lines to fit within the limit.
        # in addition we will be looking for lines that start with
        # a period '.' and append and additional period '.' to that
        # line. NOTE: this does not count towards are limit.

        # normalize the line breaks so we know the explode works
        $msg_data = str_replace("\r\n","\n",$msg_data);
        $msg_data = str_replace("\r","\n",$msg_data);
        $lines = explode("\n",$msg_data);

        # we need to find a good way to determine is headers are
        # in the msg_data or if it is a straight msg body
        # currently I'm assuming rfc 822 definitions of msg headers
        # and if the first field of the first line (':' sperated)
        # does not contain a space then it _should_ be a header
        # and we can process all lines before a blank "" line as
        # headers.
        $field = substr($lines[0],0,strpos($lines[0],":"));
        $in_headers = false;
        if(!empty($field) && !strstr($field," ")) {
            $in_headers = true;
        }

        $max_line_length = 998; # used below; set here for ease in change

        while(list(,$line) = @each($lines)) {
            $lines_out = null;
            if($line == "" && $in_headers) {
                $in_headers = false;
            }
            # ok we need to break this line up into several
            # smaller lines
            while(strlen($line) > $max_line_length) {
                $pos = strrpos(substr($line,0,$max_line_length)," ");

                # Patch to fix DOS attack
                if(!$pos) {
                    $pos = $max_line_length - 1;
                }

                $lines_out[] = substr($line,0,$pos);
                $line = substr($line,$pos + 1);
                # if we are processing headers we need to
                # add a LWSP-char to the front of the new line
                # rfc 822 on long msg headers
                if($in_headers) {
                    $line = "\t" . $line;
                }
            }
            $lines_out[] = $line;

            # now send the lines to the server
            while(list(,$line_out) = @each($lines_out)) {
                if(strlen($line_out) > 0)
                {
                    if(substr($line_out, 0, 1) == ".") {
                        $line_out = "." . $line_out;
                    }
                }
                fputs($this->smtp_conn,$line_out . $this->CRLF);
            }
        }


        # ok all the message data has been sent so lets get this
        # over with aleady
        fputs($this->smtp_conn, $this->CRLF . "." . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "DATA not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * Expand takes the name and asks the server to list all the
     * people who are members of the _list_. Expand will return
     * back and array of the result or false if an error occurs.
     * Each value in the array returned has the format of:
     *     [ <full-name> <sp> ] <path>
     * The definition of <path> is defined in rfc 821
     *
     * Implements rfc 821: EXPN <SP> <string> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 550
     * SMTP CODE ERROR  : 500,501,502,504,421
     * @access public
     * @return string array
     */
    function Expand($name) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Expand() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"EXPN " . $name . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "EXPN not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        # parse the reply and place in our array to return to user
        $entries = explode($this->CRLF,$rply);
        while(list(,$l) = @each($entries)) {
            $list[] = substr($l,4);
        }

        return $list;
    }

    /**
     * Sends the HELO command to the smtp server.
     * This makes sure that we and the server are in
     * the same known state.
     *
     * Implements from rfc 821: HELO <SP> <domain> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500, 501, 504, 421
     * @access public
     * @return bool
     */
    function Hello($host="") {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Hello() without being connected");
            return false;
        }

        # if a hostname for the HELO wasn't specified determine
        # a suitable one to send
        if(empty($host)) {
            # we need to determine some sort of appopiate default
            # to send to the server
            $host = "localhost";
        }

        // Send extended hello first (RFC 2821)
        if(!$this->SendHello("EHLO", $host))
        {
            if(!$this->SendHello("HELO", $host))
                return false;
        }

        return true;
    }

    /**
     * Sends a HELO/EHLO command.
     * @access private
     * @return bool
     */
    function SendHello($hello, $host) {
        fputs($this->smtp_conn, $hello . " " . $host . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER: " . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => $hello . " not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        $this->helo_rply = $rply;
        
        return true;
    }

    /**
     * Gets help information on the keyword specified. If the keyword
     * is not specified then returns generic help, ussually contianing
     * A list of keywords that help is available on. This function
     * returns the results back to the user. It is up to the user to
     * handle the returned data. If an error occurs then false is
     * returned with $this->error set appropiately.
     *
     * Implements rfc 821: HELP [ <SP> <string> ] <CRLF>
     *
     * SMTP CODE SUCCESS: 211,214
     * SMTP CODE ERROR  : 500,501,502,504,421
     * @access public
     * @return string
     */
    function Help($keyword="") {
        $this->error = null; # to avoid confusion

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Help() without being connected");
            return false;
        }

        $extra = "";
        if(!empty($keyword)) {
            $extra = " " . $keyword;
        }

        fputs($this->smtp_conn,"HELP" . $extra . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 211 && $code != 214) {
            $this->error =
                array("error" => "HELP not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return $rply;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command.
     *
     * Implements rfc 821: MAIL <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,421
     * @access public
     * @return bool
     */
    function Mail($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Mail() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"MAIL FROM:<" . $from . ">" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "MAIL not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * Sends the command NOOP to the SMTP server.
     *
     * Implements from rfc 821: NOOP <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500, 421
     * @access public
     * @return bool
     */

    function Noop() {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Noop() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"NOOP" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "NOOP not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * Sends the quit command to the server and then closes the socket
     * if there is no error or the $close_on_error argument is true.
     *
     * Implements from rfc 821: QUIT <CRLF>
     *
     * SMTP CODE SUCCESS: 221
     * SMTP CODE ERROR  : 500
     * @access public
     * @return bool
     */
    function Quit($close_on_error=true) {
        $this->error = null; # so there is no confusion

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Quit() without being connected");
            return false;
        }

        # send the quit command to the server
        fputs($this->smtp_conn,"quit" . $this->CRLF);

        # get any good-bye messages
        $byemsg = $this->get_lines();

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $byemsg;
        }

        $rval = true;
        $e = null;

        $code = substr($byemsg,0,3);
        if($code != 221) {
            # use e as a tmp var cause Close will overwrite $this->error
            $e = array("error" => "SMTP server rejected quit command",
                       "smtp_code" => $code,
                       "smtp_rply" => substr($byemsg,4));
            $rval = false;
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $e["error"] . ": " .
                         $byemsg . $this->CRLF;
            }
        }

        if(empty($e) || $close_on_error) {
            $this->Close();
        }

        return $rval;
    }

    /**
     * Sends the command RCPT to the SMTP server with the TO: argument of $to.
     * Returns true if the recipient was accepted false if it was rejected.
     *
     * Implements from rfc 821: RCPT <SP> TO:<forward-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,552,553,450,451,452
     * SMTP CODE ERROR  : 500,501,503,421
     * @access public
     * @return bool
     */
    function Recipient($to) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Recipient() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"RCPT TO:<" . $to . ">" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250 && $code != 251) {
            $this->error =
                array("error" => "RCPT not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * Sends the RSET command to abort and transaction that is
     * currently in progress. Returns true if successful false
     * otherwise.
     *
     * Implements rfc 821: RSET <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500,501,504,421
     * @access public
     * @return bool
     */
    function Reset() {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Reset() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"RSET" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "RSET failed",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in.
     *
     * Implements rfc 821: SEND <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     * @access public
     * @return bool
     */
    function Send($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Send() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"SEND FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "SEND not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in and send them an email.
     *
     * Implements rfc 821: SAML <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     * @access public
     * @return bool
     */
    function SendAndMail($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                "error" => "Called SendAndMail() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"SAML FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "SAML not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command. This command
     * will send the message to the users terminal if they are logged
     * in or mail it to them if they are not.
     *
     * Implements rfc 821: SOML <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,502,421
     * @access public
     * @return bool
     */
    function SendOrMail($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                "error" => "Called SendOrMail() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"SOML FROM:" . $from . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "SOML not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * This is an optional command for SMTP that this class does not
     * support. This method is here to make the RFC821 Definition
     * complete for this class and __may__ be implimented in the future
     *
     * Implements from rfc 821: TURN <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE FAILURE: 502
     * SMTP CODE ERROR  : 500, 503
     * @access public
     * @return bool
     */
    function Turn() {
        $this->error = array("error" => "This method, TURN, of the SMTP ".
                                        "is not implemented");
        if($this->do_debug >= 1) {
            echo "SMTP -> NOTICE: " . $this->error["error"] . $this->CRLF;
        }
        return false;
    }

    /**
     * Verifies that the name is recognized by the server.
     * Returns false if the name could not be verified otherwise
     * the response from the server is returned.
     *
     * Implements rfc 821: VRFY <SP> <string> <CRLF>
     *
     * SMTP CODE SUCCESS: 250,251
     * SMTP CODE FAILURE: 550,551,553
     * SMTP CODE ERROR  : 500,501,502,421
     * @access public
     * @return int
     */
    function Verify($name) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "error" => "Called Verify() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"VRFY " . $name . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250 && $code != 251) {
            $this->error =
                array("error" => "VRFY failed on name '$name'",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return $rply;
    }

    /*******************************************************************
     *                       INTERNAL FUNCTIONS                       *
     ******************************************************************/

    /**
     * Read in as many lines as possible
     * either before eof or socket timeout occurs on the operation.
     * With SMTP we can tell if we have more lines to read if the
     * 4th character is '-' symbol. If it is a space then we don't
     * need to read anything else.
     * @access private
     * @return string
     */
    function get_lines() {
        $data = "";
        while($str = fgets($this->smtp_conn,515)) {
            if($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data was \"$data\"" .
                         $this->CRLF;
                echo "SMTP -> get_lines(): \$str is \"$str\"" .
                         $this->CRLF;
            }
            $data .= $str;
            if($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data is \"$data\"" . $this->CRLF;
            }
            # if the 4th character is a space then we are done reading
            # so just break the loop
            if(substr($str,3,1) == " ") { break; }
        }
        return $data;
    }

}

?>