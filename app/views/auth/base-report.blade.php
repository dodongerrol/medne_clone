<!DOCTYPE html>
<?php //echo '<pre>';print_r($clinics); echo '</pre>';
?>
<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>Base Statistic Report </div>
        
        <div>
            <table>
                <tr>
                    <td>Clinic Id</td><td>Clinic Name</td><td>Clinic Address</td><td>Total Booking</td><td>Concluded Booking</td><td>Canceled Booking</td>
                </tr>
                <?php foreach($clinics as $clinicdetalis){ ?>
                <tr>
                    <td><?php echo $clinicdetalis['clinicid'];?></td>
                    <td><?php echo $clinicdetalis['clinicname'];?></td>
                    <td><?php echo $clinicdetalis['address'];?></td>
                    <td style="text-align: center; width: 120px"><?php echo $clinicdetalis['totalbooking'];?></td>
                    <td style="text-align: center; width: 120px"><?php echo $clinicdetalis['totalconcluded'];?></td>
                    <td style="text-align: center; width: 120px"><?php echo $clinicdetalis['totalcanceled'];?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </body>
</html>
