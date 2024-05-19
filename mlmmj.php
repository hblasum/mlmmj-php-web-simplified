Die gewünschte Aktion wird ausgeführt, falls Fehler angezeigt werden, bitte Mail an XXX.

<?php

/* Copyright (C) 2004 Christoph Thiel <ct at kki dot org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */


// error_reporting(E_ALL);

class mlmmj
{
    // echo "classdecl ";
    var $email;
    var $mailinglist;
    var $job;
    var $redirect_success;
    var $redirect_failure;

    var $delimiter;
    var $errors;

    function is_email($string="") 
	{
	    if (preg_match(chr(7)."^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]+$".chr(7).'i', $string)) 
	    { 
		return TRUE; 
	    }
	    else 
	    { 
		return FALSE; 
	    }
	}

    function error($string="")
	{
	    $this->errors = TRUE;
	    die($string);
	}

    public function __construct()
    {
	    // set mandatory vars...
	    $this->errors = FALSE;
	    $this->delimiter = "+";

	    if (!isset($_POST["email"]) &&
		!isset($_POST["mailinglist"]) &&
		!isset($_POST["job"]) &&
		!isset($_POST["redirect_success"]) &&
		!isset($_POST["redirect_failure"]))
	    {
		$this->errors = TRUE;
		if(isset($_POST["redirect_failure"]))
		{
		    header("Location: ".$_POST["redirect_failure"]);
		    exit;
		}
		else
		    echo("An error occurred.");
	    }
	    else
	    {
		if($this->is_email($_POST["email"]))
		    $this->email = $_POST["email"];
		else
		    $this->error("ERROR: email is not a valid email address.");

		if($this->is_email($_POST["mailinglist"]))
		    $this->mailinglist = $_POST["mailinglist"];
		else
		    $this->error("ERROR: mailinglist is not a valid email address.");
		
		$this->job = $_POST["job"];
		
		if(!(($this->job == "subscribe") OR ($this->job == "unsubscribe")))
		{
		    $this->error("ERROR: job unknown.");
		}
		
		//$this->redirect_failure = $_POST["redirect_failure"];
		//$this->redirect_success = $_POST["redirect_success"];

	    }

	    // now we should try to go ahead and {sub,unsub}scribe... ;)

        echo $this->errors;
        if(!$this->errors)
        //if(1)
        {
		// @ ^= char(64)
		
		$to = str_replace(chr(64),$this->delimiter.$this->job.chr(64),$this->mailinglist);
		$subject = $this->job." to ".$this->mailinglist;
		$body = $this->job;
		$addheader = "";
		$addheader .= "Received: from ". $_SERVER["REMOTE_ADDR"]
		    ." by ". $_SERVER["SERVER_NAME"]. " with HTTP;\r\n\t".date("r")."\n";
		$addheader .= "X-Originating-IP: ".$_SERVER["REMOTE_ADDR"]."\n";
		$addheader .= "X-Mailer: mlmmj-webinterface powered by PHP/". phpversion() ."\n";
		$addheader .= "From: ".$this->email."\n";
		//$addheader .= "Cc: ".$this->email."\n";

        $result = mail($to, $subject, $body, $addheader);

        echo "<p>Resultat der Anfrage ist $result (0=Fehlschlag, 1=Erfolg). Das Mailinglistenprogramm wurde aufgefordert, für die Liste mylist die Anfrage \"$this->job\" für $this->email auszuführen und eine Bestätigungsemail zu versenden, die in der Mailbox ankommen sollte."; 

        //{
		//    $this->error($this->job." failed.");
	    //}

        }
	}
}
$mailinglist = new mlmmj();
?>
