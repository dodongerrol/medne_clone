/*  xxxxxxxxxxx         Use this page to keep your database upto date     xxxxxxxxxxxxxxxxxxxxxxxx/
*   Use         :       To keep database upto date
*   Created     :       2014-12-11
*   Updated     :       
*   Created by  :       Rizvi
*/



# ================= DB changed on version =========================
# ===================== medicloud_v001 ============================

# New Update
ALTER TABLE `medi_insurance_company` ADD `Annotation_Default` VARCHAR(1500) NULL AFTER `Annotation`;

# Add custom title and webstie to clinic
ALTER TABLE `medi_clinic` ADD `Custom_title` VARCHAR(500) NULL AFTER `Description`, ADD `Website` VARCHAR(256) NULL AFTER `Custom_title`;

# For price update
ALTER TABLE `medi_clinic` ADD `Clinic_Price` VARCHAR(1500) NULL AFTER `MRT`;

# Data update 
INSERT INTO `medi_insurance_company` (`CompanyID`, `Name`, `Description`, `Image`, `Annotation`, `Annotation_Default`, `Active`) VALUES
(111, 'GP', 'GP', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428304938/yps120uxtcwuntu5npqi.png', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1441694118/gp_green_nce5o0.png', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1441694118/gp_red_zld9ma.png', 1),
(112, 'Dental', 'Dental', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428304938/yps120uxtcwuntu5npqi.png', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1441694117/dentist_green_yurgdc.png', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1441694118/dentist_red_qj7prg.png', 1),
(113, 'TCM', 'TCM', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1428304938/yps120uxtcwuntu5npqi.png', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1441694118/tcm_green_oyk06q.png', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1441694118/tcm_red_rn7ck8.png', 1);



# ============= xxxxxxx New Changes Added xxxxxxxx ===============
#                           Medicloud_v002
# ============= xxxxxx                       ===============

# Create Manage Holidays Table
CREATE TABLE `medi_manage_holidays` (
`ManageHolidayID` int(12) NOT NULL,
  `ClinicID` int(12) NOT NULL,
  `DoctorID` int(11) DEFAULT NULL,
  `Party` tinyint(1) DEFAULT NULL COMMENT '3 - Clinic, 2 - Doctor',
  `PartyID` int(12) DEFAULT NULL,
  `Type` tinyint(1) DEFAULT NULL COMMENT '0 - Full, 1 - Custom',
  `Title` varchar(256) DEFAULT NULL,
  `Holiday` varchar(20) DEFAULT NULL,
  `From_Time` varchar(20) DEFAULT NULL,
  `To_Time` varchar(20) DEFAULT NULL,
  `Created_on` int(50) DEFAULT NULL,
  `created_at` int(50) DEFAULT NULL,
  `updated_at` int(50) DEFAULT NULL,
  `Active` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Indexes for table `medi_manage_holidays`
--
ALTER TABLE `medi_manage_holidays`
 ADD PRIMARY KEY (`ManageHolidayID`);

--
-- AUTO_INCREMENT for table `medi_manage_holidays`
--
ALTER TABLE `medi_manage_holidays`
MODIFY `ManageHolidayID` int(12) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;



# Create Manage Times 
CREATE TABLE `medi_manage_times` (
`ManageTimeID` int(11) NOT NULL,
  `Party` tinyint(1) DEFAULT NULL COMMENT '3 - Clinic, 2 - Doctor',
  `PartyID` int(12) DEFAULT NULL,
  `ClinicID` int(11) DEFAULT NULL,
  `DoctorID` int(12) DEFAULT NULL,
  `Type` tinyint(1) DEFAULT NULL COMMENT '0 - 24, 1 - Custom',
  `From_Date` varchar(50) DEFAULT NULL,
  `To_Date` varchar(50) DEFAULT NULL,
  `Repeat` tinyint(1) DEFAULT NULL COMMENT '0 - No, 1 - Yes',
  `Status` tinyint(1) DEFAULT NULL COMMENT '0 - Inactive, 1 - Active',
  `Created_on` int(50) DEFAULT NULL,
  `created_at` int(50) DEFAULT NULL,
  `updated_at` int(50) DEFAULT NULL,
  `Active` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Indexes for table `medi_manage_times`
--
ALTER TABLE `medi_manage_times`
 ADD PRIMARY KEY (`ManageTimeID`);

--
-- AUTO_INCREMENT for table `medi_manage_times`
--
ALTER TABLE `medi_manage_times`
MODIFY `ManageTimeID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;


# Added Manage time Id and Party in Clinic Time
ALTER TABLE `medi_clinic_time` ADD `ManageTimeID` INT(12) NULL AFTER `ClinicTimeID`, ADD `Party` TINYINT(1) NULL AFTER `ManageTimeID`;

# Added Doctor id in Clinic Time
ALTER TABLE `medi_clinic_time` ADD `DoctorID` INT(12) NULL AFTER `ClinicID`;



# Create Clinic Procedures
CREATE TABLE `medi_clinic_procedure` (
`ProcedureID` int(12) NOT NULL,
  `ClinicID` int(12) NOT NULL,
  `Name` varchar(500) DEFAULT NULL,
  `Description` varchar(1500) DEFAULT NULL,
  `Duration` int(7) DEFAULT NULL,
  `Duration_Format` varchar(20) DEFAULT NULL,
  `Price` double(5,2) DEFAULT NULL,
  `Created_on` int(30) DEFAULT NULL,
  `created_at` int(30) DEFAULT NULL,
  `updated_at` int(30) DEFAULT NULL,
  `Active` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Indexes for table `medi_clinic_procedure`
--
ALTER TABLE `medi_clinic_procedure`
 ADD PRIMARY KEY (`ProcedureID`);

--
-- AUTO_INCREMENT for table `medi_clinic_procedure`
--
ALTER TABLE `medi_clinic_procedure`
MODIFY `ProcedureID` int(12) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;




# Create Doctor Procedures
CREATE TABLE `medi_doctor_procedure` (
`DoctorProcedureID` int(12) NOT NULL,
  `ProcedureID` int(12) NOT NULL,
  `ClinicID` int(12) NOT NULL,
  `DoctorID` int(12) NOT NULL,
  `Created_on` int(30) DEFAULT NULL,
  `created_at` int(30) DEFAULT NULL,
  `updated_at` int(30) DEFAULT NULL,
  `Active` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Indexes for table `medi_doctor_procedure`
--
ALTER TABLE `medi_doctor_procedure`
 ADD PRIMARY KEY (`DoctorProcedureID`);

--
-- AUTO_INCREMENT for table `medi_doctor_procedure`
--
ALTER TABLE `medi_doctor_procedure`
MODIFY `DoctorProcedureID` int(12) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;

# Add clinic type in medi_clinic 
ALTER TABLE `medi_clinic` ADD `Clinic_Type` INT( 2 ) NULL AFTER `Name` ;


# xxxxxxx Update on 20-12-2015 xxxxxxxxxxxxxx

ALTER TABLE `medi_user_appoinment` ADD `ClinicTimeID` INT( 12 ) NULL AFTER `UserID` ,
ADD `DoctorID` INT( 12 ) NULL AFTER `ClinicTimeID` ,
ADD `ProcedureID` INT( 12 ) NULL AFTER `DoctorID` ,
ADD `StartTime` VARCHAR( 30 ) NULL AFTER `ProcedureID` ,
ADD `EndTime` VARCHAR( 30 ) NULL AFTER `StartTime` ,
ADD `Remarks` VARCHAR( 1000 ) NULL AFTER `EndTime` ;


# Change Procedure price digits 
ALTER TABLE `medi_clinic_procedure` CHANGE `Price` `Price` DOUBLE(7,2) NULL DEFAULT NULL;


# Add phone code on user table 
ALTER TABLE `medi_user` ADD `PhoneCode` VARCHAR(5) NULL AFTER `FIN`;

# change the price format in clinic Procedure 
ALTER TABLE `medi_clinic_procedure` CHANGE `Price` `Price` INT(7) NULL DEFAULT NULL;

-- nhr 29-1-2016
ALTER TABLE `medi_doctor` 
ADD COLUMN  `gmail` VARCHAR(255) NULL COMMENT '' ,
ADD COLUMN  `token` TEXT NULL COMMENT '' ;

-- 2016-2-2
-- // nhr chek google event oe medicloud event gc event=1
ALTER TABLE `medi_user_appoinment` 
ADD COLUMN `Gc_event_id` VARCHAR(45) NULL COMMENT '' AFTER `Active`,
ADD COLUMN `event_type` tinyint(1) COMMENT '0-medi, 1-gc, 3-widget' default 1;


# Add phone code and Emergency code 
ALTER TABLE `medi_doctor` ADD `Code` VARCHAR(5) NULL AFTER `image`, ADD `Emergency_Code` VARCHAR(5) NULL AFTER `Code`;


# Change price format to Varchar - 29-02-2016
-- /////////////////////////////////////////////v3////////////////////////////////////////////////////////////
ALTER TABLE `medi_clinic_procedure` CHANGE `Price` `Price` VARCHAR(10) NULL DEFAULT NULL;

-- // 3rd party events loading nhr 2016-4-01
CREATE TABLE `medi_extra_events` (
  `id` VARCHAR(255) NULL COMMENT '',
  `type` TINYINT(1) NULL COMMENT '1-gc event ',
  `date` VARCHAR(30) NULL COMMENT '',
  `start_time` VARCHAR(45) NULL COMMENT '',
  `end_time` VARCHAR(30) NULL COMMENT '',
  `doctor_id` INT(11) NULL COMMENT '',
  `event_id` VARCHAR(45) NULL COMMENT '',
  `remarks` VARCHAR(255) NULL COMMENT '');

-- // add address & zip code fields into medi_user table and change length of state field // 2016-04-06

ALTER TABLE `medi_user`
ADD COLUMN `Address` VARCHAR(256) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT '' AFTER `Lng`,
ADD COLUMN `Zip_Code` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT '' AFTER `State`,
CHANGE COLUMN `State` `State` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '' ;

--  nhr .....................................................

ALTER TABLE `medi_user_appoinment` 
ADD COLUMN `Duration` INT(11) NULL COMMENT 'In minutes' AFTER `event_type`,
ADD COLUMN `Price` VARCHAR(10) NULL COMMENT '' AFTER `Duration`;

-- ......................activity log..................

CREATE TABLE `medi_activity_log` (
  `id` VARCHAR(20) NOT NULL COMMENT '',
  `user_pin` INT(10) NULL COMMENT '',
  `date_time` VARCHAR(15) NULL COMMENT '',
  `activity_header` VARCHAR(45) NULL COMMENT '',
  `activity` VARCHAR(255) NULL COMMENT '',
  `updated_at` INT(11) NULL COMMENT '',
  `created_at` INT(11) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '');

-- Add new comment for status field -- 

ALTER TABLE `medi_user_appoinment` 
CHANGE COLUMN `Status` `Status` INT(2) NOT NULL COMMENT '0 - Active, 1 - Procesing, 2 - Completed, 3 - Disabled, 4 - No Show' ;

-- //stafff - nhr  16-04-2016

CREATE TABLE `medi_staff` (
  `staff_id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NULL COMMENT '',
  `designation` VARCHAR(45) NULL COMMENT '',
  `qualifcation` VARCHAR(100) NULL COMMENT '',
  `email` VARCHAR(45) NULL COMMENT '',
  `phone` VARCHAR(45) NULL COMMENT '',
  `pin_no` INT(4) NULL COMMENT '',
  `updated_at` INT NULL COMMENT '',
  `created_at` INT NULL COMMENT '',
  `active` INT(1) NULL COMMENT '0-inactive, 1-active',
  PRIMARY KEY (`staff_id`)  COMMENT '');


-- //////////////////nhr 2016-4-25
ALTER TABLE `medi_staff` 
ADD COLUMN `clinic_id` VARCHAR(45) NULL COMMENT '' AFTER `active`;

-- //////////////////nhr 2016-4-27

ALTER TABLE `medi_doctor` 
ADD COLUMN `cc_email` VARCHAR(45) NULL DEFAULT NULL COMMENT ' ' AFTER `token`,
ADD COLUMN `check_login` INT(1) NULL DEFAULT 0 COMMENT '' AFTER `cc_email`,
ADD COLUMN `check_pin` INT(1) NULL DEFAULT 0 COMMENT '' AFTER `check_login`,
ADD COLUMN `check_sync` INT(1) NULL DEFAULT 0 COMMENT '' AFTER `check_pin`,
ADD COLUMN `pin`  VARCHAR(4) NULL DEFAULT '0000' COMMENT '' AFTER `check_sync`;


-- nhr 2016/5/2

ALTER TABLE `medi_staff` 
ADD COLUMN `cc_email` VARCHAR(45) NULL DEFAULT NULL COMMENT '' AFTER `clinic_id`;


-- nhr 2016/5/3
ALTER TABLE `medi_extra_events` 
CHANGE COLUMN `type` `type` TINYINT(1) NULL DEFAULT NULL COMMENT '1-gc event 2-blocker, 3-breaks' ,
ADD COLUMN `updated_at` INT NULL DEFAULT NULL COMMENT '' AFTER `remarks`,
ADD COLUMN `created_at` INT NULL DEFAULT NULL COMMENT '' AFTER `updated_at`,
ADD COLUMN `day` VARCHAR(5) NULL DEFAULT NULL COMMENT '' AFTER `created_at`;


-- (2016/5/3) -- 

-- medi_manage_holidays table changes --

ALTER TABLE `medi_manage_holidays` 
ADD COLUMN `From_Holiday` VARCHAR(20) NULL DEFAULT NULL COMMENT '' AFTER `Holiday`,
ADD COLUMN `To_Holiday` VARCHAR(20) NULL DEFAULT NULL COMMENT '' AFTER `From_Holiday`,
ADD COLUMN `Note` VARCHAR(256) NULL DEFAULT NULL COMMENT '' AFTER `To_Time`;


ALTER TABLE `medi_extra_events` 
CHANGE COLUMN `id` `id` VARCHAR(255) NOT NULL COMMENT '' ,
ADD PRIMARY KEY (`id`)  COMMENT '';


ALTER TABLE `medi_staff` 
ADD COLUMN `check_login` INT(1) NULL DEFAULT '0' COMMENT '' AFTER `cc_email`;


-- (2016/05/13)
-- Add Calendar Account setting DB tables 

ALTER TABLE `medi_clinic` 
ADD COLUMN `Calendar_type` INT(1) NULL DEFAULT 1 COMMENT '1 - Weekly 2 - Daily' AFTER `Opening`,
ADD COLUMN `Calendar_day` INT(1) NULL DEFAULT 1 COMMENT '1 - Monday 2 - Tuesday 3 - Wednesday 4 - Thursday 5 - Friday 6 - Saturday 7 - Sunday' AFTER `Calendar_type`,
ADD COLUMN `Calendar_duration` INT(10) NULL DEFAULT 15 COMMENT 'Minutes' AFTER `Calendar_day`;


-- nhr 2016/5/16

ALTER TABLE `medi_clinic` 
ADD COLUMN `Favourite` INT(1) NULL DEFAULT 0 COMMENT 'favourite = 1 (from mobile) ' AFTER `Calendar_duration`;

-- (2016/05/17)
-- Add Personalized_Message field into clinic table

ALTER TABLE `medi_clinic` 
ADD COLUMN `Personalized_Message` TEXT NULL DEFAULT NULL COMMENT '' AFTER `Favourite`;

-- nhr2016-5-18
ALTER TABLE `medi_extra_events` 
ADD COLUMN `clinic_id` INT(11) NULL COMMENT '' AFTER `doctor_id`;


-- nhr clinic user favourite
CREATE TABLE `medi_clinic_user_favourite` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `user_id` INT NULL COMMENT '',
  `clinic_id` INT NULL COMMENT '',
  `favourite` INT(1) NULL COMMENT 'favourite-1, unfavourite-0 ',
  `updated_at` INT NULL COMMENT '',
  `created_at` INT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '');

-- Update clinic calendar type comment --
ALTER TABLE `medi_clinic` 
CHANGE COLUMN `Calendar_type` `Calendar_type` INT(1) NULL DEFAULT '1' COMMENT '1 - Weekly 2 - Daily 3 - Monthly' ;

-- Add Pone code field into clinic table

ALTER TABLE `medi_clinic` 
ADD COLUMN `Phone_Code` VARCHAR(10) NULL DEFAULT NULL COMMENT '' AFTER `Lng`;


--Add Start Hour into clinic table (2016/06/20)

ALTER TABLE `medi_clinic` 
ADD COLUMN `Calendar_Start_Hour` VARCHAR(30) NULL DEFAULT '12:00 AM' COMMENT '' AFTER `Calendar_duration`;


-- pin rquire for clinicwise
ALTER TABLE `medi_clinic` 
ADD COLUMN `Require_pin` INT(1) NULL DEFAULT 0 COMMENT '1 - require, 0- not require' AFTER `Calendar_Start_Hour`;

-- 2016/06/22
-- Add Doctor Phone Code

ALTER TABLE `medi_doctor` 
ADD COLUMN `phone_code` VARCHAR(10) NULL DEFAULT NULL COMMENT '' AFTER `Emergency_Code`;

-- Add Staff Phone Code

ALTER TABLE `medi_staff` 
ADD COLUMN `phone_code` VARCHAR(10) NULL DEFAULT NULL COMMENT '' AFTER `email`;


-- v3.2------------2016-7-20----------

ALTER TABLE `medi_clinic_procedure` 
CHANGE COLUMN `created_at` `created_at` DATE NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `updated_at` `updated_at` DATE NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `medi_doctor` 
CHANGE COLUMN `created_at` `created_at` DATE NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `updated_at` `updated_at` DATE NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `medi_staff` 
CHANGE COLUMN `created_at` `created_at` DATE NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `medi_user` 
CHANGE COLUMN `created_at` `created_at` DATE NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `updated_at` `updated_at` DATE NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `medi_user_appoinment` 
CHANGE COLUMN `created_at` `created_at` DATE NOT NULL COMMENT '' ,
CHANGE COLUMN `updated_at` `updated_at` DATE NOT NULL COMMENT '' ;

-- 2016-7-25 for booking source
ALTER TABLE `medi_user` 
ADD COLUMN `source_type` INT(1) NULL DEFAULT 0 COMMENT '1-web, 2-mobile, 3-widget' AFTER `Active`;


-- v3.3 capture sms history

CREATE TABLE `medi_sms_history` (
  `id` INT NOT NULL COMMENT '',
  `name` VARCHAR(45) NULL COMMENT '',
  `phone_code` VARCHAR(45) NULL COMMENT '',
  `phone_number` VARCHAR(45) NULL COMMENT '',
  `message` TEXT(10000) NULL COMMENT '',
  `clinic_id` INT NULL COMMENT '',
  `created_at` DATETIME NULL COMMENT '',
  `updated_at` DATETIME NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '');
