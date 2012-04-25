<?php

class WebPage {
	private $ajax = false;
	private $page = 'welcome';
	private $noCookies = false;
	
	private $generalCookieName = 'generalInfo-Apr-22-2012';
	private $generalQuestions = array();
	private $questionCookieName = 'harderToLiveForGodYN-Apr-22s-2012';
	private $questionCookieAnswer = '';
	private $cookieServer = '';
	private $generalInfoFile = 'gen-info.html';
	private $questionFile = 'question-apr-22-2012.txt';

	function __construct(){
		$this->cookieServer = (strpos($_SERVER['HTTP_HOST'],'localhost') === 0) ? "" : $_SERVER['HTTP_HOST'];
		$this->page = (!empty($_GET['pg'])) ? $_GET['pg'] : 'welcome';
		$this->composeGeneralQuestions();
		$this->verifyGeneralInfoFileExists();
		$this->verifyQuestionInfoFileExists();
		$this->director();
	}
	private function director(){
		if(!$this->ajax){
			$this->drawHeader();
		}
		switch($this->page){
			case 'question':
				$this->drawQuestion();
				break;
			case 'answer':
				$this->answerQuestion();
				break;
			case 'general':
				$this->drawGeneralInfo();
				break;
			case 'general_answers':
				$this->answerGeneralInfo();
				break;
			case 'welcome':
			case 'home':
			default:
				$this->drawWelcome();
				break;
		}
		
		if(!$this->ajax){
			$this->drawFooter();
		}
	}
	function verifyGeneralInfoFileExists(){
		if(!file_exists($this->generalInfoFile)){
			$fp = fopen($this->generalInfoFile, 'a');
			fwrite($fp, "<html><head><title>General Info</title><link rel=stylesheet href='spring-2012.css' type='text/css'></head><body>");
			fclose($fp);
			chmod($this->generalInfoFile,0777);
		}
	}
	function writeNewGeneralInfo($content){
		$fp = fopen($this->generalInfoFile, 'a');
		fwrite($fp, $content.'<hr>');
		fclose($fp);
	}
	function verifyQuestionInfoFileExists(){
		if(!file_exists($this->questionFile)){
			$fp = fopen($this->questionFile, 'a');
			fwrite($fp, "0\n0");
			fclose($fp);
			chmod($this->questionFile,0777);
		}
	}
	function writeNewAnswer($answerWasYes=true){
		$file = file($this->questionFile);
		$file[0] = trim($file[0]);
		if($answerWasYes){
			$file[0]++;
		} else {
			$file[1]++;
		}
		$fp = fopen($this->questionFile, 'w');
		fwrite($fp, $file[0]."\n".$file[1]);
		fclose($fp);
	}
	function composeGeneralQuestions(){
		$this->generalQuestions = array(
		'Name'=>'longtext',
		'Email'=>'longtext',
		'Age'=>'shorttext',
		'Grade'=>'shorttext',
		'Baptised'=>'yesno',
		'Holy Ghost'=>'yesno',
		'Allergies'=>'textbox',
		'Strange Fact'=>'longtext',
		'Favorite Subject'=>'longtext',
		'Favorite Colors'=>'textbox',
		'Favorite Foods'=>'textbox',
		'Least Fav Foods'=>'textbox',
		'Hobbies'=>'textbox',
		'School Activites'=>'textbox',
		'Favorite Bands'=>'textbox',
		'Favorite Songs'=>'textbox',
		);
	} 
	function drawHeader(){
		print "<html>
		<head><title>Youth 2012</title>
		<link rel=stylesheet href='spring-2012.css' type='text/css'> 
		<script type='text/javascript' src='jquery-1.7.2.min.js'></script>
		</head>
		<body>
		<div align='center'>
		<div align='left' id='mainBody'>
		<strong class='heading'>Youth Spring 2012</strong><br>
		<a href='index.php?pg=welcome'>Home</a> | <a href='index.php?pg=general'>General</a> | <a href='index.php?pg=question'>Question</a>";
		if(file_exists($this->generalInfoFile)){
			print " | <a href='".$this->generalInfoFile."' style='color:blue;'>Students</a>";
		}
		if(file_exists($this->questionFile)){
			print " | <a href='".$this->questionFile."' style='color:blue;'>Answers</a>";
		}
		
		print "<br>";
	} 
	function drawFooter(){
		print "</div></div></body></html>";
	}
	function drawWelcome(){
		print "Welcome!";
	}
	function drawGeneralInfo(){
		if(!empty($_COOKIE[$this->generalCookieName])){
			print "You have already filled this information out.";
		} else {
			$compoundIfStatement = "";
			$compoundIfStatementArray = array();
			print "<form action='index.php?pg=general_answers' method='post'>";
			print "<table width='400' cellspacing='0'>";
			foreach($this->generalQuestions as $question=>$type){
				$inputName = str_replace(' ','_',$question);
				print "<tr>";
				switch($type){
					case 'longtext':
						$compoundIfStatementArray[] = "jQuery('#{$inputName}).val().length>=5";
						print "<td width='150' align='right'>{$question}:&nbsp;</td>";
						print "<td width='250' align='left' valign='top'><input type='text' name='{$inputName}' id='{$inputName}' style='width:250px;' value=''></td>";
						break;
					case 'text':
						$compoundIfStatementArray[] = "jQuery('#{$inputName}).val().length>=3";
						print "<td width='150' align='right'>{$question}:&nbsp;</td>";
						print "<td width='250' align='left' valign='top'><input type='text' name='{$inputName}' id='{$inputName}' style='width:150px;' value=''></td>";
						break;
					case 'shorttext':
						$compoundIfStatementArray[] = "jQuery('#{$inputName}).val().length>=1";
						print "<td width='150' align='right'>{$question}:&nbsp;</td>";
						print "<td width='250' align='left' valign='top'><input type='text' name='{$inputName}' id='{$inputName}' style='width:20px;' value=''></td>";
						break;
					case 'textbox':
						$compoundIfStatementArray[] = "jQuery('#{$inputName}).val().length>=4";
						print "<td width='150' align='right' valign='top'>{$question}:&nbsp;</td>";
						print "<td width='250' align='left' valign='top'><textarea name='{$inputName}' id='{$inputName}' style='width:250px;' rows='3'></textarea></td>";
						break;
					case 'yesno':
						$compoundIfStatementArray[] = "jQuery('#{$inputName}).val().length>=3";
						print "<td width='150' align='right'>{$question}:&nbsp;</td>";
						print "<td width='250' align='left' valign='top'><input type='radio' name='{$inputName}' value='Yes'> Yes &nbsp; <input type='radio' name='{$inputName}' value='No'> No</td>";
						break;
				}
				print "</tr>";
			}
			$compoundIfStatement = implode("&&",$compoundIfStatementArray);
			$compoundIfStatement = "confirm('Are you sure you are done?')";
			print "<tr><td>&nbsp;</td><td><input type='button' value=\"I'm Done!\" onclick=\"if({$compoundIfStatement}){this.form.submit();}\">";
			print "</table>";
			print "</form>";
		}
	}
	function answerGeneralInfo(){
		if(count($_POST) && empty($_COOKIE[$this->generalCookieName])){
			$content = "<table width='400' cellspacing='0'>";
			$content .= "<td width='150' align='right' valign='top'>Date:&nbsp;</td>";
			$content .= "<td width='250' align='left' valign='top'>".date('m/d/Y')."</td>";
			foreach($this->generalQuestions as $question=>$type){
				$inputName = str_replace(' ','_',$question);
				$_POST[$inputName] = htmlspecialchars($_POST[$inputName]);
				$content .= "<tr>";
				if($type == 'textbox'){
					$content .= "<td width='150' align='right' valign='top'>{$question}:&nbsp;</td>";
					$content .= "<td width='250' align='left' valign='top'>".nl2br(trim($_POST[$inputName]))."</td>";
				} else {
					$content .= "<td width='150' align='right'>{$question}:&nbsp;</td>";
					$content .= "<td width='250' align='left' valign='top'>".trim($_POST[$inputName])."</td>";
				}
				$content .= "</tr>";
			}
			$content .= "</table>";
			$this->writeNewGeneralInfo($content);
			
			print "The following information has been saved:<br>".$content;
			if(!$this->noCookies){
				setcookie($this->generalCookieName, 1, strtotime('+6 days'), "/", $this->cookieServer);
			}
		} else if(!empty($_COOKIE[$this->generalCookieName])){
			print "We've already got your information, but thanks for being persistant!";
		}
	}
	function drawQuestion(){
		if(!empty($_COOKIE[$this->questionCookieName])){
			print "It is harder to live for God now than it was in earlier times.<br>";
			print "You answered <b>{$_COOKIE[$this->questionCookieName]}</b>"; 	
		} else {
			print "<form action='index.php?pg=answer' method='post'>";
			print "It is harder to live for God now than it was in earlier times.<br>";
			print "<input type='checkbox' value='Yes' name='answer1' onclick='this.form.answer2.checked=false'> Yes<br>";
			print "<input type='checkbox' value='No' name='answer2' onclick='this.form.answer1.checked=false'> No<br>";
			print "<input type='button' value='Answer!' onclick=\"if(this.form.answer1.checked||this.form.answer2.checked){this.form.submit();}else{alert('Please select your answer.')}\">";
			print "</form>";
		}
	}
	function answerQuestion(){
		if(!empty($_COOKIE[$this->questionCookieName])){
			$this->questionCookieAnswer = $_COOKIE[$this->questionCookieName]; 
		} else {
			$answerWasYes = false;
			if(!empty($_POST['answer1'])){
				$questionAnswer = $_POST['answer1'];
				$answerWasYes = true;
			} else if (!empty($_POST['answer2'])){
				$questionAnswer = $_POST['answer2'];
				$answerWasYes = false;
			}
			$this->questionCookieAnswer = $questionAnswer;
			if(!$this->noCookies){
				setcookie($this->questionCookieName, $questionAnswer, strtotime('+1 year'), "/", $this->cookieServer);
			}
			$this->writeNewAnswer($answerWasYes);
		}
		print "It is harder to live for God now than it was in earlier times.<br>";
		print "You answered <b>{$this->questionCookieAnswer}</b>"; 	 
	}
	
	
}

?>
