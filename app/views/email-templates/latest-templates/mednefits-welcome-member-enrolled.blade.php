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

      <div style="position:relative;min-height: 500px;background: #fff;padding: 50px 30px;">
        <!-- <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/new_welcome_email_header.png" style="width: 100%;"> -->

        <div style="width: 100%;text-align: center;height: 90px;border-bottom: 2px solid #ddd;margin-bottom: 50px;">
          <img src="https://s3-ap-southeast-1.amazonaws.com/mednefits/images/Mednefits Logo (BLUE).png" style="width: 220px;">
        </div>

        <div style="padding: 0 20px">
          <p>Dear {{ $name }},</p>

          <p style="font-weight: 700;font-size: 25px;margin: 30px 0;color: #0392cf;">We are excited to have you on-board!</p>

          <p>Your company <a href="" style="color: #333;font-size: 17px;font-weight: 700">{{ $company }}</a> has enrolled you into the Mednefits health benefits program. This program allows you to experience amazing health benefits that are simple and human - the kind we want for ourselves, and our loved ones.</p>

          <p>Your plan will start on <span style="text-decoration: underline;color: #0392cf;font-size: 17px;font-weight: 700">{{ $start_date }}</span></p>

          <p style="font-weight: 700;font-size: 25px;margin: 40px 0 20px 0;color: #0392cf;">Member Account Login</p>

          <p style="margin: 0"><span style="font-weight: 700;font-size: 17px;display: inline-block;width: 100px">Login ID:</span> <span style="text-decoration: underline;">{{ $email }}</span></p>
          <p style="margin-top: 0"><span style="font-weight: 700;font-size: 17px;display: inline-block;width: 100px">Password:</span> {{ $pw }} </p>

          <p style="margin: 20px 0 40px 0">Get <b>Mednefits App</b> on <a href="https://itunes.apple.com/sg/app/mednefits-better-benefits/id972694931?mt=8" style="color: #333;">Apple App Store</a> or <a href="https://play.google.com/store/apps/details?id=com.sg.medicloud&hl=en" style="color: #333;">Android PlayStore</a> </p>

          <p style="font-weight: 700;font-size: 25px;margin: 40px 0 20px 0;color: #0392cf;">Here's your welcome pack!</p>

          <p style="margin-bottom: 30px;">Guide and Coverage:</p>

          <a href="https://s3-ap-southeast-1.amazonaws.com/mednefits/pdf/Mednefits+Tutorial+for+Members.pdf" style="width:170px;margin: 0 30px 30px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">DOWNLOAD GUIDE</a>
          @if($plan)
            @if($plan['account_type'] == 'stand_alone_plan')
              <a href="https://s3-ap-southeast-1.amazonaws.com/mednefits/pdf/Members+Coverage.pdf" style="width:170px;margin: 0 30px 30px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">VIEW COVERAGE</a>
            @elseif($plan['account_type'] == 'lite_plan')
              <a href="https://s3-ap-southeast-1.amazonaws.com/mednefits/e-template-img/lite_plan_coverage.png" style="width:170px;margin: 0 30px 30px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">VIEW COVERAGE</a>
            @else
              <a href="https://s3-ap-southeast-1.amazonaws.com/mednefits/pdf/Members+Coverage.pdf" style="width:170px;margin: 0 30px 30px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">VIEW COVERAGE</a>
            @endif
          @else
            <a href="https://s3-ap-southeast-1.amazonaws.com/mednefits/pdf/Members+Coverage.pdf" style="width:170px;margin: 0 30px 30px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">VIEW COVERAGE</a>
          @endif  
          
          
          <!-- <p style="margin-bottom: 30px;">In-Network Partners:</p> -->
          <p style="margin-bottom: 30px;">Panel Partners:</p>


          <a href="https://docs.google.com/spreadsheets/d/1YtsLDjgdHu6bKkZWRGtBIdeyWhwPTnDdQGFrUsBOZ9g/pubhtml" style="width:170px;margin: 0 30px 30px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">LOCATE PARTNERS</a>

          <a href="https://docs.google.com/presentation/d/e/2PACX-1vQL5p31afwKGNPnXfIsP3m7JooApc7BhfBKfxKfghYElXG0wxZING1c57Rxrqbbm829k2Lj3tsc-BVn/pub?start=false&loop=false&delayms=3000" style="width:170px;margin: 0 30px 30px 0;padding: 14px 0px;border: none;border-radius: 4px;background: #3b84f1;color: #FFF;display: inline-block;font-size: 12px;text-decoration: none;text-align: center;">WELLNESS PRIVILEGES</a>

          <p style="font-size: 25px;color: #0392cf;font-weight: 700;margin-top: 60px;">Need help?</p>

          <p>We are always here to help. Should you encounter any issues or have any questions, feel free to contact us:</p>

          <p style="margin:0;font-weight: 700">You may ring us</p>
          <p style="margin:0;">+65 3163 5403</p>
          <p style="margin:0;">+60 330 995 774</p>
          <p style="margin-top:0;">Mon - Fri 9:00am to 6:00pm</p>

          <p style="margin:0;font-weight: 700">Drop us a note, anytime</p>
          <p style="margin-top:0;margin-bottom: 40px;">support@mednefits.com</p>


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
