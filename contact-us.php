<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contact Us, AudioBionics Inc.</title>
<link href="styles.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #6BC4FF}
.style2 {color: #000000}
-->
</style>
<script type="text/javascript">
<!--
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
    } if (errors) alert('The following error(s) occurred:\n'+errors);
    document.MM_returnValue = (errors == '');
} }
//-->
</script>
</head>

<body>
<div class="main">
  <div class="header"><img src="images/logo.png" alt="Audio Bionics" width="960" height="204" /></div>
        <div class="navBar">
          <?php require_once('menu.php'); ?>
  </div>
        <div class="content">
        <div class="contentTop"></div>
        <div class="contentBG">
        <h1>Contact Us</h1>
        <div class="info"><table width="427" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td width="310">Toll Free</td>
    <td width="310" bgcolor="#D5EFFF">800-807-5724</td>
  </tr>
  <tr>
    <td>Telephone</td>
    <td width="310" bgcolor="#D5EFFF"><span class="style2">626-355-7158</span></td>
  </tr>
  <tr>
    <td>Email</td>
    <td width="310" bgcolor="#D5EFFF"><span class="style1"><a href='&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#105;&#110;&#102;&#111;&#64;&#97;&#117;&#100;&#105;&#111;&#98;&#105;&#111;&#110;&#105;&#99;&#115;&#46;&#99;&#111;&#109;'>Click to Send Email</a>
</span></td>
  </tr>
  <tr>
    <td colspan="2"><h2>Dealer Inquiries</h2></td>
    </tr>
  <tr>
    <td>General Inquiries</td>
    <td bgcolor="#D5EFFF"><a href="mailto:info@audiobionics.com">Click To Send Email</a></td>
  </tr>
  <tr>
    <td>Sales Dept.</td>
    <td bgcolor="#D5EFFF"><a href='&#109;&#97;&#105;&#108;&#116;&#111;&#58;&#115;&#97;&#108;&#101;&#115;&#64;&#97;&#117;&#100;&#105;&#111;&#98;&#105;&#111;&#110;&#105;&#99;&#115;&#46;&#99;&#111;&#109;'>Click to Send Email</a></td>
  </tr>
</table>
</div>
       <div class="formContainer">
       <h2>Send Us An Email</h2>
       <p>Please fill out the form below, we will personally answer your question as soon as possible.
</p>
       <table width="370" border="0" cellspacing="5" cellpadding="5">
          <tr>
            <td colspan="2"></td>
          </tr>
          <form action="/HDWForm2Excel/Form2Excel.php" method="post" onsubmit="MM_validateForm('name','','R','Company','','R','Email','','NisEmail','Phone','','R','Zip','','NisNum');return document.MM_returnValue">
            <tr>
              <td width="89">Name *</td>
              <td width="246" bgcolor="#D5EFFF"><input type="text" name="name" id="name" /></td>
            </tr>
            <tr>
              <td>Company </td>
              <td bgcolor="#D5EFFF"><input type="text" name="Company" id="Company" /></td>
            </tr>
            <tr>
              <td>Email *</td>
              <td bgcolor="#D5EFFF"><input type="text" name="Email" id="Email" /></td>
            </tr>
            <tr>
              <td>Phone *</td>
              <td bgcolor="#D5EFFF"><input type="text" name="Phone" id="Phone" /></td>
            </tr>
            <tr>
              <td>Address</td>
              <td bgcolor="#D5EFFF"><input type="text" name="Address" id="Address" /></td>
            </tr>
            <tr>
              <td>City</td>
              <td bgcolor="#D5EFFF"><input type="text" name="City" id="City" /></td>
            </tr>
            <tr>
              <td>State</td>
              <td bgcolor="#D5EFFF"><select name="State">
                  <option value="" selected="selected">Select a State</option>
                  <option value="AL">Alabama</option>
                  <option value="AK">Alaska</option>
                  <option value="AZ">Arizona</option>
                  <option value="AR">Arkansas</option>
                  <option value="CA">California</option>
                  <option value="CO">Colorado</option>
                  <option value="CT">Connecticut</option>
                  <option value="DE">Delaware</option>
                  <option value="DC">District Of Columbia</option>
                  <option value="FL">Florida</option>
                  <option value="GA">Georgia</option>
                  <option value="HI">Hawaii</option>
                  <option value="ID">Idaho</option>
                  <option value="IL">Illinois</option>
                  <option value="IN">Indiana</option>
                  <option value="IA">Iowa</option>
                  <option value="KS">Kansas</option>
                  <option value="KY">Kentucky</option>
                  <option value="LA">Louisiana</option>
                  <option value="ME">Maine</option>
                  <option value="MD">Maryland</option>
                  <option value="MA">Massachusetts</option>
                  <option value="MI">Michigan</option>
                  <option value="MN">Minnesota</option>
                  <option value="MS">Mississippi</option>
                  <option value="MO">Missouri</option>
                  <option value="MT">Montana</option>
                  <option value="NE">Nebraska</option>
                  <option value="NV">Nevada</option>
                  <option value="NH">New Hampshire</option>
                  <option value="NJ">New Jersey</option>
                  <option value="NM">New Mexico</option>
                  <option value="NY">New York</option>
                  <option value="NC">North Carolina</option>
                  <option value="ND">North Dakota</option>
                  <option value="OH">Ohio</option>
                  <option value="OK">Oklahoma</option>
                  <option value="OR">Oregon</option>
                  <option value="PA">Pennsylvania</option>
                  <option value="RI">Rhode Island</option>
                  <option value="SC">South Carolina</option>
                  <option value="SD">South Dakota</option>
                  <option value="TN">Tennessee</option>
                  <option value="TX">Texas</option>
                  <option value="UT">Utah</option>
                  <option value="VT">Vermont</option>
                  <option value="VA">Virginia</option>
                  <option value="WA">Washington</option>
                  <option value="WV">West Virginia</option>
                  <option value="WI">Wisconsin</option>
                  <option value="WY">Wyoming</option>
              </select></td>
            </tr>
            <tr>
              <td>Zip Code?</td>
              <td bgcolor="#D5EFFF"><input type="text" name="Zip" id="Zip" /></td>
            </tr>
            <tr>
              <td>Questions?</td>
              <td bgcolor="#D5EFFF"><textarea name="Questions" id="Questions" cols="45" rows="5"></textarea></td>
            </tr>
            <tr>
              <td colspan="2"><input type="image" src="images/send.png" value="submit" name="button" id="button" /></td>
            </tr>
            <input type="hidden" name="hdwuploadfolder" id="hdwuploadfolder" value="files" />
            <input type="hidden" name="hdwok" id="hdwok" value="../thank-you.php" />
            <input type="hidden" name="hdwemail" id="hdwemail" value="&#105;&#110;&#102;&#111;&#64;&#97;&#117;&#100;&#105;&#111;&#98;&#105;&#111;&#110;&#105;&#99;&#115;&#46;&#99;&#111;&#109;" />
            <input type="hidden" name="hdwnook" id="hdwnook" value="../error.php" />
          </form>
        </table>
        </div>
        <div class="clear"></div>
        </div>
        <div class="contentFooterBG"></div>
          <?php require_once('footer.php'); ?>
  </div>
        <?php require_once('tracking.php'); ?>

</body>
</html>
