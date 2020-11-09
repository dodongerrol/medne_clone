<!DOCTYPE html>
<html><head>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="/assets/new_landing/images/favicon.ico" type="image/ico">
    <title>Spending Invoice Transactions</title>
    <style type="text/css">
      @page { 
        margin: 10px 10px 0 10px; 
      }

      * {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
      }

      body{
        margin: 0;
        font-family: 'Helvetica Light', sans-serif;
        font-size: 14px;
        line-height: 1.42857143;
      }

      #main-template-wrapper{
        border: 1px solid #ccc;
        width: 100%;
        /* max-width: 774px; */
        max-width: 1080px;
        background: #fff;
        /* height: 1080px; */
        height: 761px;
        margin: 0 auto;
        /* padding: 20px 0; */
      }

      
    </style>
  </head><body>
    <div id="main-template-wrapper" >
      <table border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0;color: #0D0D0D;" width="100%">
        <tr>
          <td align="right" valign="top" colspan="2" style="padding: 30px 40px 10px 0px;">
            <img src="https://mednefits.s3-ap-southeast-1.amazonaws.com/images/mobile-logo-blue-latest.png" style="width: 180px;">
          </td>
        </tr>
        <tr>
          <td style="width: 60%;padding-left: 40px;vertical-align: top;">
            <p style="font-size: 20px;line-height: 29px;margin: 0 0 30px 0;">Transaction History Statement</p>
            <p style="font-size: 12px;line-height: 14px;margin: 0 0 10px 10px;">
              {{ $company }}<br>
              Attention: {{ $statement_contact_name }}<br>
              {{ $company_address }}, {{$building_name}}, {{$unit_number}}<br>
              {{ $postal }}<br>
            </p>
          </td>
          <td style="vertical-align: top;padding-right: 40px;padding-bottom: 35px;text-align: right;">
            <p style="font-size: 11px;line-height: 13px;margin: 0;">
            @if($currency_type == "sgd")
              <span>
                Medicloud Pte Ltd<br>
                7 Temasek Boulevard #18-02 Suntec Tower One, S(038987)<br>
                mednefits.com
              </span>
            @else
              <span>
                Mednefits Sdn Bhd<br>
                Komune, Level 2,<br>
                No. 20, Jalan Kerinchi Kiri 3,<br>
                59200, Kuala Lumpur,<br>
                Malaysia<br>
              </span>
            @endif
            </p>
            <div>
              <p style="font-size: 11px;line-height: 14px;margin: 16px 0 12px 0;">Page 1 of 1</p>
              <table border="0" cellpadding="0" cellspacing="0" style="margin: 0; padding: 0;text-align: left;font-size: 12px;line-height: 1;border-bottom: 1.5px solid #404040;" width="100%">
                <tr>
                  <td colspan="2" style="background: #037CB0;border-radius: 5px 5px 0px 0px;color: #FFF;padding: 5px 8px;">
                    YOUR COMPANY STATEMENT
                  </td>
                </tr>
                <tr>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    Panel Monthly Spending For
                  </td>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    {{ $period }}
                  </td>
                </tr>
                <tr>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    Transaction Breakdown For 
                  </td>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    Invoice no. {{ $statement_number }}
                  </td>
                </tr>
                <tr>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    Total Spent
                  </td>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    {{ strtoupper($currency_type) }} {{ $statement_total_amount }}
                  </td>
                </tr>
                <tr>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    Total Transactions
                  </td>
                  <td style="border-bottom: 0.5px solid #848484;padding: 5px 8px;">
                    {{ $total_transactions }}
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="padding-left: 40px;padding-bottom: 30px;font-size: 12px;">
            <div style="width: 150px;display: inline-block;vertical-align: middle;text-align: center;">
              <p style="margin: 0;max-height: 48px;height: 48px;background-color: #2E4057;color: #fff;padding: 0px 6px;line-height: 20px;display: inline-block;width: 100%;box-sizing: border-box;">
                GP - MEDICINE/TREATMENT
              </p>
              <p style="margin: 0;height: 33px;border-radius: 0px 0px 5px 5px;border: 1px solid #848484;border-width: 0 1px 1px 1px;line-height: 25px;">
              {{ strtoupper($currency_type) }} {{ $total_gp_medicine }}
              </p>
            </div>
            <div style="display:inline-block;vertical-align: middle;width: 30px;text-align: center;">+</div>
            <div style="width: 150px;display: inline-block;vertical-align: middle;text-align: center;">
              <p style="margin: 0;max-height: 48px;height: 48px;background-color: #2E4057;color: #fff;padding: 0px 6px;line-height: 36px;display: inline-block;width: 100%;box-sizing: border-box;">
                GP - CONSULTATION
              </p>
              <p style="margin: 0;height: 33px;border-radius: 0px 0px 5px 5px;border: 1px solid #848484;border-width: 0 1px 1px 1px;line-height: 25px;">
              {{ strtoupper($currency_type) }} {{ $total_gp_consultation }}
              </p>
            </div>
            <div style="display:inline-block;vertical-align: middle;width: 30px;text-align: center;">+</div>
            <div style="width: 150px;display: inline-block;vertical-align: middle;text-align: center;">
              <p style="margin: 0;max-height: 48px;height: 48px;background-color: #2E4057;color: #fff;padding: 0px 6px;line-height: 36px;display: inline-block;width: 100%;box-sizing: border-box;">
                DENTAL
              </p>
              <p style="margin: 0;height: 33px;border-radius: 0px 0px 5px 5px;border: 1px solid #848484;border-width: 0 1px 1px 1px;line-height: 25px;">
              {{ strtoupper($currency_type) }} {{ $total_dental }}
              </p>
            </div>
            <div style="display:inline-block;vertical-align: middle;width: 30px;text-align: center;">+</div>
            <div style="width: 150px;display: inline-block;vertical-align: middle;text-align: center;">
              <p style="margin: 0;max-height: 48px;height: 48px;background-color: #2E4057;color: #fff;padding: 0px 6px;line-height: 36px;display: inline-block;width: 100%;box-sizing: border-box;">
                TCM
              </p>
              <p style="margin: 0;height: 33px;border-radius: 0px 0px 5px 5px;border: 1px solid #848484;border-width: 0 1px 1px 1px;line-height: 25px;">
              {{ strtoupper($currency_type) }} {{ $total_tcm }}
              </p>
            </div>
          </td>
        </tr>
      </table>
      
      <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 0 80px 0; padding: 0 40px;color: #0D0D0D;font-size: 12px;" width="100%">
        <tr>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 30px 5px 0;vertical-align: top;">DATE</td>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 30px 5px 0;vertical-align: top;">MEMBER</td>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 30px 5px 0;vertical-align: top;">TRANSACTION NO.</td>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 30px 5px 0;vertical-align: top;">ITEM/SERVICE</td>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 30px 5px 0;vertical-align: top;">PROVIDER</td>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 30px 5px 0;vertical-align: top;">TOTAL AMOUNT</td>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 30px 5px 0;vertical-align: top;">MEDICINE/TREATMENT</td>
          <td style="font-size: 12px;border-bottom: 0.5px solid #848484;padding: 0 0 5px 0;vertical-align: top;text-align: right;">CONSULTATION</td>
        </tr>
        @foreach($in_network as $key => $trans)
        <tr>
          <td style="padding: 18px 30px 0 0;vertical-align: top;">
          {{ $trans['date_of_transaction'] }}
          </td>
          <td style="padding: 18px 30px 0 0;vertical-align: top;">
          {{ $trans['member'] }}
          </td>
          <td style="padding: 18px 30px 0 0;vertical-align: top;">
          {{ $trans['transaction_id'] }}
          </td>
          <td style="padding: 18px 30px 0 0;vertical-align: top;">
          {{ $trans['clinic_type_and_service'] }}
          </td>
          <td style="padding: 18px 30px 0 0;vertical-align: top;">
          {{ $trans['clinic_name'] }}
          </td>
          <td style="padding: 18px 30px 0 0;vertical-align: top;">
            {{ $trans['currency_type'] }} {{ $trans['total_amount'] }}
          </td>
          <td style="padding: 18px 30px 0 0;vertical-align: top;">
            {{ $trans['currency_type'] }} {{ $trans['treatment'] }}
          </td>
          <td style="padding: 18px 0 0 0;vertical-align: top;text-align: right;">
            {{ $trans['currency_type'] }} {{ $trans['consultation'] }}
          </td>
        </tr>
        @endforeach
      </table>

    </div>
    </body></html>
