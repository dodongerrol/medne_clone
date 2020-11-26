<!doctype html>
<html>
  <head>
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>
    EMPLOYEE WELCOME EMAIL. (once member are enrolled and account created)
    Subject: WELCOME TO MEDNEFITS CARE
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

    #main-template-wrapper{
      margin: 80px auto;
    }

    @media only screen and (max-width: 700px) {
      #main-template-wrapper{
        margin: 0;
      }
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

  </head>

  <body style="font-family:'Arial';box-sizing: border-box;margin: 0;padding: 0;display: block;background-color: #F1F1F1">

    <div id="main-template-wrapper" style="width: 800px;height: 100%;position: relative;overflow: hidden;box-shadow: -1px 2px 2px #bbb;">

      <div style="position:relative;min-height: 500px;background: #fff;padding: 70px 60px 30px;">
        <!-- <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/new_welcome_email_header.png" style="width: 100%;"> -->

        <div style="width: 100%;text-align: center;height: 60px;border-bottom: 2px solid #ddd;margin-bottom: 50px;">
          <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/images/mobile-logo-blue-latest.png" style="width: 220px;">
        </div>

        <div>
          <p style="font-size: 18px;">Hi {{ $emailName }},</p>

          <p style="margin: 0 0 25px 0; font-size: 15px;line-height: 18px;">
            Your company has enrolled you into the Mednefits health benefits program. Please click the following button to download Mednefits App and use this registered mobile number <span>{{$code}} {{$phone}}</span> as your login id to create your password during account activation.
          </p>

          <div style="text-align: center; margin: 30px 0 50px;">
            <a href="https://bridgeurl.com/mednefits-app" style="display:inline-block;text-decoration: none;background-color: #3192CF; border-radius: 4px; color: #fff; border: 0; width: 331px; font-size: 15px; padding: 8px 0; outline: none;">Download Mednefits</a>
          </div>

          <div style="border-bottom: 2px solid #ddd;width: 100%;"></div>

          <p style="font-size: 18px;color: #0392cf;font-weight: 400;margin: 60px 0 0;">Need help?</p>

          <p style="margin: 0;">We are always here to help. Should you encounter any issues or have any questions, feel free to contact us:</p>

          <p style="margin:0;font-weight: 700">You may ring us</p>
          <p style="margin:0;">+65 3163 5403</p>
          <p style="margin:0;">+60 330 995 774</p>
          <p style="margin:0;">Mon - Fri 9:00am to 6:00pm</p>

          <p style="margin:0;font-weight: 700">Drop us a note, anytime</p>
          <p style="margin-top:0;margin-bottom: 40px;color: #0392cf;text-decoration: underline;">happiness@mednefits.com</p>


          <p style="margin: 0;">Thank you</p>
          <p style="margin: 0;">Your Mednefits Team</p>

        </div>
      </div>

    </div>

  </body>
</html>
