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

    <div id="main-template-wrapper" style="width: 700px;height: 100%;position: relative;overflow: hidden;box-shadow: -1px 2px 2px #bbb;">

      <div style="position:relative;min-height: 500px;background: #fff;padding: 50px 50px;">
        <!-- <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/new_welcome_email_header.png" style="width: 100%;"> -->

        <div style="width: 100%;text-align: center;height: 90px;border-bottom: 2px solid #999;margin-bottom: 50px;">
          <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits Logo (BLUE).png" style="width: 220px;">
        </div>

        <div>
          <div style="border-bottom: 2px solid #999;padding-bottom: 30px;">
            <p style="font-size: 18px;">Hi {{$emailName}},</p>

            <p style="margin: 16px 0 0;">You have been appointed as an administrator of Mednefits.</p>
            <p style="margin: 0 0 16px;">You are now able to:</p>

            <div>
              <ul>
                <li>
                  <span>View employee + dependent profile information</span>
                </li>
                <li>
                  <span>Edit employee + dependent profiles</span>
                </li>
                <li>
                  <span>Enroll & terminate employee + dependent</span>
                </li>
                <li>
                  <span>Approve, reject & edit non-panel claims</span>
                </li>
              </ul>
            </div>

            <p>The permissions above will be applied to: All Employees & Dependents</p>
          </div>

          <p style="font-size: 25px;color: #0392cf;font-weight: 400;margin-top: 40px;">Need help?</p>

          <p>We are always here to help. Should you encounter any issues or have any questions, feel free to contact us:</p>

          <p style="margin:0;font-weight: 700">You may ring us</p>
          <p style="margin:0;">+65 3163 5403</p>
          <p style="margin:0;">+60 330 995 774</p>
          <p style="margin-top:0;">Mon - Fri 10:00 to 19:00</p>

          <p style="margin:0;font-weight: 700">Drop us a note, anytime</p>
          <p style="margin-top:0;margin-bottom: 40px;">happiness@mednefits.com</p>


          <p style="">Thank you</p>
          <p style="margin-bottom: 40px">Your Mednefits Team</p>

          <div style="width: 100%;border-top: 2px solid #999;padding-top: 10px;display: inline-block;">
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
              <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/mednefits+logo+v3+(blue-box)+LARGE.png" style="width: 35px;margin-bottom: 10px;">

              <p style="color: #807b7b;margin: 0;font-size: 12px;">7 Temasek Boulevard, #18-02,</p>
              <p style="color: #807b7b;margin: 0;font-size: 12px;">Suntec Tower One, Singapore 038987</p>
            </div>
          </div>
        </div>
      </div>

    </div>

  </body>
</html>
