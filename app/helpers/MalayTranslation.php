<?php

	class MalayTranslation {
        public static function malayMessages($type)
        {
            if($type == "unregistered") {
                return "Belum didaftarkan.";
            }

            if($type == "user_not_exist") {
                return "Pengguna tidak wujud.";
            }

            if($type == "invalid_otp") {
                return "OTP Tidak Sah";
            }

            if($type == "user_active_login") {
                return "Pengguna aktif, sila log masuk!";
            }

            if($type == "password_mismatch") {
                return "Kata Laluan Tak Sepadan";
            }

            if($type == "invalid_credentials") {
                return "ID atau laluan tidak tepat";
            }

            if($type == "issue_reset_pass_email") {
                return "Harap maaf, alamat e-mel anda belum didaftarkan dengan Mednefits";
            }

            if($type == "issue_reset_pass_mobile") {
                return "Harap maaf, telefon anda belum didaftarkan dengan Mednefits";
            }

            if($type == "spending_hold_registration") {
                return ["head" => "Pendaftaran ditangguhkan", "sub" => "Maaf, anda tidak mempunyai kredit untuk mengakses ciri ini buat masa ini. Sila hubungi HR anda untuk maklumat lebih lanjut."];
            }

            if($type == "spending_hold_registration_no_credits") {
                return ["head" => "Pendaftaran ditangguhkan", "sub" => "Maaf, anda tidak mempunyai kredit untuk mengakses ciri ini buat masa ini. Sila hubungi HR anda untuk maklumat lebih lanjut."];
            }

            if($type == "spending_block_user") {
                return ["head" => "Pendaftaran ditangguhkan", "message" => "Maaf, akaun anda tidak diaktifkan untuk mengakses ciri ini buat masa ini.", "sub" => "Sila hubungi HR anda untuk maklumat lebih lanjut."];
            }

            if($type == "enterprise_execeed_limit") {
                return ["head" => "14/14 lawatan digunakan", "sub" => "Nampaknya anda telah mencapai maksimum 14 lawatan pada penggal ini."];
            }

            if($type == "e_claim_block") {
                return ["head" => "E-Tuntutan Tidak Tersedia", "sub" => "Sorry, your account is not enabled to access this feature at the moment. Kindly contact your HR for more detail."];
            }

            if($type == "e_claim_block_no_credits") {
                return ["head" => "Pendaftaran ditangguhkan", "message" => "Maaf, anda tidak mempunyai kredit untuk mengakses ciri ini buat masa ini.", "sub" => "Sila hubungi HR anda untuk maklumat lebih lanjut."];
            }

            if($type == "e_claim_block_no_wellness") {
                return ["head" => "Pendaftaran ditangguhkan", "message" => "Maaf, akaun anda tidak diaktifkan untuk mengakses ciri ini buat masa ini.", "sub" => "Sila hubungi HR anda untuk maklumat lebih lanjut."];
            }

            if($type == "e_claim_not_eligible") {
                return ["head" => "Bukan Panel Tidak Tersedia", "message" => "Ahli tidak layak untuk transaksi bukan panel."];
            }

            if($type == "enterprise_execeed_limit_e_claim") {
                return ["head" => "Bukan Panel Tidak Tersedia", "message" => "Nampaknya anda telah mencapai maksimum 14 lawatan pada penggal ini."];
            }

            if($type == "enterprise_execeed_limit_e_claim_a_e") {
                return ["head" => "2/2 A&E digunakan", "message" => "Nampaknya anda telah mencapai maksimum 2 A&E yang diluluskan pada istilah ini."];
            }

            if($type == "e_claim_not_eligible_transaction") {
                return ["head" => "Bukan Panel Tidak Tersedia", "message" => "Fungsi Bukan Panel dilumpuhkan untuk syarikat anda."];
            }

            if($type == "e_claim_pending_transaction") {
                return ["head" => "Bukan Panel Tidak Tersedia", "message" => "Maaf, kami tidak dapat memproses tuntutan anda. Anda mempunyai tuntutan yang sedang menunggu persetujuan dan mungkin melebihi had kredit anda. Anda mungkin ingin menghubungi pentadbir faedah syarikat anda untuk maklumat lebih lanjut."];
            }

            if($type == "e_claim_no_credits") {
                return ["head" => "Bukan Panel Tidak Tersedia", "message" => "Harap Maaf, anda tidak mempunyai kredit yang mencukupi untuk transaksi ini. Sila hubungi HR anda untuk maklumat lebih lanjut."];
            }

        }

        public static function benefitsCategoryTranslate($category)
        {
            if(strtolower($category) == "screening") {
                return "Pemeriksaan Kesihatan";
            }

            if(strtolower($category) == "gp") {
                return "GP";
            }

            if(strtolower($category) == "dental") {
                return "Pergigian";
            }

            if(strtolower($category) == "tcm") {
                return "TCM";
            }

            if(strtolower($category) == "specialist") {
                return "Pakar";
            }

            if(strtolower($category) == "wellness") {
                return "Kesihatan";
            }

            return $category;
        }

        public static function eclaimCategory($category)
        {
            if(strtolower($category) == "general practice" || strtolower($category) == "general practices") {
                return "Doktor Umum";
            }

            if(strtolower($category) == "health screening") {
                return "Permeriksaan";
            }

            if(strtolower($category) == "dental") {
                return "Pergigian";
            }

            if(strtolower($category) == "traditional chinese medicine") {
                return "Perubatan Tradisional Cina";
            }

            if(strtolower($category) == "medical specialist") {
                return "Pakar Perubatan";
            }

            if(strtolower($category) == "other") {
                return "Lain-lain";
            }

            if(strtolower($category) == "accident & emergency") {
                return "Kemalangan dan Kecemasan";
            }

            if(strtolower($category) == "specialist") {
                return "Pakar";
            }

            if(strtolower($category) == "vision") {
                return "Penglihatan";
            }

            if(strtolower($category) == "fitness") {
                return "Kecergasan";
            }

            return $category;
        }

        public static function benefitsPlanCategory($category)
        {
            if(strtolower($category) == "general practice" || strtolower($category) == "general practices" || strtolower($category) == "outpatient gp") {
                return "Doktor Umum";
            }

            if(strtolower($category) == "health screening") {
                return "Permeriksaan";
            }

            if(strtolower($category) == "dental" || strtolower($category) == "dental care") {
                return "Pergigian";
            }

            if(strtolower($category) == "traditional chinese medicine" || strtolower($category) == "tcm") {
                return "Perubatan Tradisional Cina";
            }

            if(strtolower($category) == "medical specialist") {
                return "Pakar Perubatan";
            }

            if(strtolower($category) == "health specialist") {
                return "Pakar";
            }

            if(strtolower($category) == "other") {
                return "Lain-lain";
            }

            if(strtolower($category) == "accident & emergency") {
                return "Kemalangan dan Kecemasan";
            }

            if(strtolower($category) == "specialist") {
                return "Pakar";
            }

            if(strtolower($category) == "vision") {
                return "Penglihatan";
            }

            if(strtolower($category) == "fitness") {
                return "Kecergasan";
            }

            if(strtolower($category) == "wellness benefits") {
                return "Kesihatan";
            }

            if(strtolower($category) == "as charged") {
                return "Seperti Yang Dikenakan";
            }

            if(strtolower($category) == "out_of_pocket_description") {
                return "Ini menunjukkan bahawa tidak ada rancangan yang berlaku untuk membolehkan peruntukan hak faedah pekerja, yang bermaksud semua pekerja akan dikenakan bayaran untuk kos penggunaan faedah mereka sendiri.";
            }

            return $category;
        }

        public static function servicesCategory($category)
        {
            if(strtolower($category) == "medicine & treatment" || strtolower($category) == "medicine and treatment") {
                return "Ubat & Rawatan";
            }

            if(strtolower($category) == "consultation" || strtolower($category) == "consultations") {
                return "Perundingan";
            }

            if(strtolower($category) == "health screening") {
                return "Permeriksaan";
            }

            if(strtolower($category) == "procedure") {
                return "Prosedur";
            }

            return $category;
        }

        public static function currencyTranslation($currency)
        {
            if($currency == "sgd") {
                return "SGD - Singapura Dollar";
            }
        }

        public static function monthTransalation($month)
        {
            if(strtolower($month) == "jan") {
                return "Januari";
            }

            if(strtolower($month) == "feb") {
                return "Februari";
            }

            if(strtolower($month) == "mar") {
                return "Mac";
            }

            if(strtolower($month) == "apr") {
                return "April";
            }

            if(strtolower($month) == "may") {
                return "Mei";
            }

            if(strtolower($month) == "jun") {
                return "Jun";
            }

            if(strtolower($month) == "jul") {
                return "Julai";
            }

            if(strtolower($month) == "aug") {
                return "Ogos";
            }

            if(strtolower($month) == "sep") {
                return "September";
            }

            if(strtolower($month) == "oct") {
                return "Oktober";
            }

            if(strtolower($month) == "nov") {
                return "November";
            }

            if(strtolower($month) == "dec") {
                return "Desimber";
            }
        }

        public static function dayTransalation($day)
        {
            if(strtolower($day) == "mon") {
                return "Isnin";
            }

            if(strtolower($day) == "tue") {
                return "Selasa";
            }

            if(strtolower($day) == "wed") {
                return "Rabu";
            }

            if(strtolower($day) == "thu") {
                return "Khamis";
            }

            if(strtolower($day) == "fri") {
                return "Jumaat";
            }

            if(strtolower($day) == "sat") {
                return "Sabtu";
            }

            if(strtolower($day) == "sun") {
                return "Ahad";
            }
        }

        public static function statusTextTranslate($text) 
        {
            if(strtolower($text) == "approved") {
                return "Diluluskan";
            }

            if(strtolower($text) == "pending") {
                return "Untuk Diselesai";
            }

            if(strtolower($text) == "rejected") {
                return "Ditolak";
            }

            return $text;
        }
        
        public static function extraTextTranslate($text) 
        {
            if(strtolower($text) == "not applicable") {
                return "Tidak Berkenaan”";
            }

            if(strtolower($text) == "open") {
                return "Beroperasi”";
            }

            if(strtolower($text) == "closed") {
                return "Tutup”";
            }

            if(strtolower($text) == "as charged" || strtolower($text) == "as charge") {
                return "Seperti Yang Dikenakan";
            }

            return $text;
        }
	}
?>