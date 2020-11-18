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
                return "Maaf, alamat e-mel anda belum didaftarkan dengan Mednefits";
            }

            if($type == "issue_reset_pass_mobile") {
                return "Maaf, telefon anda belum didaftarkan dengan Mednefits";
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
                return "Permeriksaan";
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

        public static function currencyTranslation($currency)
        {
            if($currency == "sgd") {
                return "SGD - Singapura Dollar";
            }
        }
	}
?>