<!doctype html>
<html><head>
  <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    Reset Password Email
    Subject:  Forgot your company password?
  </title>

  <style type="text/css">
    .social-contact{
      text-align: center;
      margin: 20px 0 40px 0;
    }

    .social-icons{
      text-align: center;
      margin: 20px 0;
    }

    @media only screen and (max-width: 485px) {
      .social-contact{
        float: none;
        display: block;
      }

      .social-icons{
        float: none;
        display: block;
      }
    }
  </style>

  </head><body style="font-family:'Arial';box-sizing: border-box;margin: 0;padding: 0;display: block;background-color: #F1F1F1">

    <div id="main-template-wrapper" style="width: 700px;height: 100%;position: relative;overflow: hidden;background: #FFF;margin: 50px auto;box-shadow: -1px 2px 2px #bbb;">

      <div style="position:relative;min-height: 500px;padding: 50px">
        <div style="width: 100%;text-align: center;height: 100px;border-bottom: 2px solid #ddd;margin-bottom: 50px;">
          <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits Logo (BLUE).png" style="width: 220px;">
        </div>

        <p style="margin-top: 10px;margin-bottom: 40px;">Dear {{ $name }},</p>

        <p style="font-size: 25px;color: #0392cf;font-weight: 700;">{{ $context }}</p>

        <p style="margin-bottom: 0px;">That's okay, it happens!</p>

        <p style="margin-top: 0px;margin-bottom: 30px;">Click on the button below to reset your password.</p>

        <a href="{{ $activeLink }}" style="width:186px;margin: 0 0 40px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">SET NEW PASSWORD</a>

        <p style="margin: 30px 0;">If you did not request to reset your password, ignore this email and the link will expire on its own. </p>

        <p style="font-size: 25px;color: #0392cf;font-weight: 700;margin-top: 60px;">Need help?</p>

        <p>We are always here to help. Should you encounter any issues or have any questions, feel free to contact us:</p>

        <p style="margin:0;font-weight: 700">You may ring us</p>
        <p style="margin:0;">Singapore: +65 3163 5403</p>
          <p style="margin:0;">Malaysia: +60 330 995 774</p>
        <p style="margin-top:0;">Mon - Fri 9:00am to 6:00pm</p>

        <p style="margin:0;font-weight: 700">Drop us a note, anytime</p>
        <p style="margin-top:0;margin-bottom: 40px;">support@mednefits.com</p>


        <p style="font-weight: 700">Thank you</p>
        <p style="font-weight: 700;margin-bottom: 20px">Your Mednefits Team</p>

        <div style="width: 100%;border-top: 2px solid #999;padding-top: 30px;">
          <div class="social-icons" style="">
            <div style="display: inline-block;">
              <a href="https://www.facebook.com/Mednefits/">
                <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/facebook-2.png" style="width: 35px;float: left;margin-right: 10px;">
              </a>
              <a href="https://www.youtube.com/channel/UC-V-ZvH3HWCgpkjvYUiUv1w">
                <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/YouTube.png" style="width: 35px;float: left;margin-right: 10px;">
              </a>
              <a href="https://www.linkedin.com/company/mednefits/">
                <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/linkedin-2.png" style="width: 35px;float: left;">
              </a>
            </div>
          </div>

          <div class="social-contact">
            <img src="http://admin.medicloud.sg/images/mednefits+logo+v3+(blue-box)+LARGE.png" style="width: 35px;margin-bottom: 10px;">

            <p style="color: #807b7b;margin: 0;font-size: 12px;">7 Temasek Boulevard, #18-02,</p>
            <p style="color: #807b7b;margin: 0;font-size: 12px;">Suntec Tower One, Singapore 038987</p>
          </div>
        </div>

      </div>

    </div>
  </body></html>
