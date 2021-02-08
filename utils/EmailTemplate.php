<?php

namespace App\utils;

class EmailTemplate
{

  public static function welcome($recipient, $confirmationlink)
  {
    return '<p>Dear ' . $recipient . ',</p>
          <div>Thank you for registering with us. To complete your registration,<br/><br/>
              <a style="padding: 10px; border:none;background-color:#4caf50;color:white;margin: 10px auto;" href="' . $confirmationlink . '">Click here</a>
              </div>
              <br/>
              <p>to confirm your account and complete your profile for approval and verification</p>
              <p>If the above doesn\'t work, use the link below <br/><br/>
                ' . $confirmationlink . '
              </p><br/>
              <p>This Link will expire in 24 hours</p>
    
          <p>Thanks,<br/>owi Team</p>';
  }


  public static function forgotpassword($recipient, $token, $confirmationlink)
  {
    return '<p>Dear ' . $recipient . ',</p>
          <div>You are recieving this email because you requested to reset your password</div>
          <div>Use this token to reset your password</div>
          <div style="text-align: center"><b><h1>' . $token . '</h1></b></div>
          <div><a style="margin: 10px;" href="' . $confirmationlink . '">Proceed to Reset Password</a></div>
          <div>This Token will expire in 20 minutes</div>
          <div>If this was not requested by you, contact support</div>
    
          <p>Thanks,<br/>owi Team</p>';
  }
}
