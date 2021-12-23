<?php

namespace App\utils;

use App\config\DotEnv;

(new DotEnv(__DIR__ . '/../.env'))->load();

class EmailTemplate
{

  public static function welcome($recipient, $confirmationlink)
  {
    return '<p>Dear ' . $recipient . ',</p>
          <div>Thank you for registering with us. To complete your registration,<br/><br/>
              <a style="padding: 10px; border:none;text-decoration:none;background-color:#4caf50;color:white;margin: 10px auto;" href="' . $confirmationlink . '">Click here</a>
              </div>
              <br/>
              <p>to confirm your account and complete your profile for approval and verification</p>
              <p>If the above doesn\'t work, use the link below <br/><br/>
                ' . $confirmationlink . '
              </p><br/>
              <p>This Link will expire in 24 hours</p>
    
          <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function welcomeuser($recipient, $email, $password)
  {
    return '<p>Dear ' . $recipient . ',</p>
          <div>Your Account has been created,<br/><br/>
              <div>Use the credentials below to login to your Account</div>
              <div>Email: ' . $email . '</div>
              <div>Password: ' . $password . '</div>
              <div style="margin-top: 50px;">
              <a style="padding: 10px;text-decoration:none;border:none;background-color:#4caf50;color:white;margin: 10px auto;" href="' . getenv("BASE_URL") . '/admin/login">Click here</a>
            </div>
              </div>
              <br/>
              <p>to login to your account</p>
              <p>Once you are logged in, you can update your profile and change your password</p>
    
          <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function welcomeagent($recipient, $email, $password)
  {
    return '<p>Dear ' . $recipient . ',</p>
          <div>Your Account has been created,<br/><br/>
              <div>Use the credentials below to login to your Account</div>
              <div>Email: ' . $email . '</div>
              <div>Password: ' . $password . '</div>
              <div style="margin-top: 50px;">
              <a style="padding: 10px;text-decoration:none;border:none;background-color:#4caf50;color:white;margin: 10px auto;" href="' . getenv("BASE_URL") . '/agents/login">Click here</a>
            </div>
              </div>
              <br/>
              <p>to login to your account</p>
              <p>Once you are logged in, you can update your profile and change your password</p>
    
          <p>Thanks,<br/>Confidebat Team</p>';
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
    
          <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function neworder()
  {
    return '<h3 style="text-align: center;">A New Order Delivery Request has been recieved</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function cancelorder($id)
  {
    return '<h3 style="text-align: center;">Order #' . $id . ' has been cancelled</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function waybillrecieved()
  {
    return '<h3 style="text-align: center;">Your waybill pacakage has been recieved</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function deliveredorder($id)
  {
    return '<h3 style="text-align: center;">Order #' . $id . ' has been delivered</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function waybillrequest()
  {
    return '<h3 style="text-align: center;">Your have a new waybill request</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function task($count)
  {
    return '<h3 style="text-align: center;">' . $count . ' Deliveries has been assigned to you</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function waybillsent($destination)
  {
    return '<h3 style="text-align: center;">Your items has been sent to ' . $destination . ' as instructed</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }

  public static function waybillrequestcancelled($id)
  {
    return '<h3 style="text-align: center;">Waybill request with ID - ' . $id . ' has been cancelled by client</h3>
    <p>Thanks,<br/>Confidebat Team</p>';
  }
}
