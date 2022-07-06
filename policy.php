<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--
            <link rel="shortcut icon" href="http://www.salespanda.com/images/favicon.ico" />
        -->

        <title></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!--
           <script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">





        <link rel="stylesheet" href="css/style.css">

        <style>


            header.white-transparent 
            {
                height: 97px;
                background: #ffffff;
                padding: 26px;
            }




            .btn
            {
                background-color:#23292C;
                color:#ffffff;    
            }

            .btn.active, .btn:active {
                background-color:#C7222A;
                color:#ffffff;
            }

            .imgcircle{
                border: 24px solid #d7d7d7;
                margin-left: 100px;
            }

            input[name=search] 
            {
                background-image: url('searchicon.png');
            }


            @media only screen and (max-width: 600px) 
            {
                .carousel-caption 
                {
                    display:none;
                }

                .imgcircle
                {
                    margin-left:auto;
                }

                .menu-resp
                {
                    z-index:1; 
                }

            }


            @media only screen and (max-width: 460px) 
            {
                .sp-blog-detail
                {
                    height:auto !important; 
                }

                .nav-item a 
                {
                    color:#272727 !important;   
                }

                .company-tagline
                {
                    font-size:14px !important;
                }

                .nav-tabs>li 
                {

                    width:160px!important;
                }

                .menu-resp
                {
                    z-index:1; 
                }



            }

            #search-suggest{float:left;list-style:none;margin-top:-3px;padding:0;width:400px;position: absolute;z-index:1;}
            #search-suggest li{padding: 3px 0 0 13px;
                               background: #f5f5f5;
                               border: 1px solid #23292C;
                               border-top: 0px solid #23292C;
                               color: #23292C;
                               font-size: 14px;
                               text-align: left;}
            #search-suggest li:hover{background:#C7222A;cursor: pointer;color: #ffffff;}
            #search-box{padding: 10px;border: #a8d4b1 1px solid;border-radius:4px;}

            .btn.focus, .btn:focus, .btn:hover
            {
                color: #fff;
                text-decoration: none;
            }

            .input-group-addon {
                padding: .375rem 1.75rem;

            }

            .nav-tabs>li 
            {

                width:190px;
            }



            .nav-tabs>li>a {

                color: #272727;
            }

            select.form-control:not([size]):not([multiple]) {
                height:auto;
            }

            .nav-tabs img {
                display: block;
            }

            .nav > li > a > img {
                max-width: none;
            }

            .nav-tabs a {
                text-align: center;
                padding: 10px 28px !important;
            }


            .nav-tabs>li>a {

                height: 130px;
            }



            li.nav-title {
                display: block;
                text-transform: uppercase;
                font-size: 26px;
                color: #ffffff;
                min-width: 238px;
                font-weight:bold;
                font-family: 'PFHandbookPro-Regular' !important;
                padding-bottom: 32px;
                margin-top: -11px;
                font-weight:bold;
            }
            /*new added*/
            .mrt-0 {
                float: left;
                margin: 0 !important;
                text-align: left;
                width:10% !important;
            }
            .understand{
                font-size: 17px;
            }
            .term-set{
                padding:4px;	
            }
            /*End*/
            #loader {
                position: absolute;
                left: 50%;
                top: 50%;
                z-index: 1;
                width: 150px;
                height: 150px;
                margin: -75px 0 0 -75px;
                border: 16px solid #f3f3f3;
                border-radius: 50%;
                border-top: 16px solid #3498db;
                width: 120px;
                height: 120px;
                -webkit-animation: spin 2s linear infinite;
                animation: spin 2s linear infinite;
            }

            @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Add animation to "page content" */
            .animate-bottom {
                position: relative;
                -webkit-animation-name: animatebottom;
                -webkit-animation-duration: 1s;
                animation-name: animatebottom;
                animation-duration: 1s
            }

            @-webkit-keyframes animatebottom {
                from { bottom:-100px; opacity:0 } 
                to { bottom:0px; opacity:1 }
            }

            @keyframes animatebottom { 
                from{ bottom:-100px; opacity:0 } 
                to{ bottom:0; opacity:1 }
            }

        </style>
    </head>
    <body>


        <div class="main-content ng-scope">

            <section id="sp-about" class="sp-about overview-block-ptb <?php echo $newMicro ?>">
                <div class="container">
                    <div class="row">
                        <?php
                        require realpath(__DIR__ . '/vendor/autoload.php');

                        include("includes/global.php");



                        header("Access-Control-Allow-Origin: *");

                        unset($_SESSION['contName']);
                        unset($_SESSION['solName']);

                        //error_reporting(E_ALL);
                        //ini_set('display_errors', 1);
//~ ini_set('display_startup_errors', 1);

                        require_once 'includes/connect-new.php';

                        
                        $obj = new \Microsite\Microsite($connPDO);


                        if ($obj->microsite_exists === false) {
                            header("location: microsite-notfound.php");
                            exit;
                        }

                        $c_lient_Id = $obj->client_id;
                        $p_client_id = $obj->parent_id;
                        $pcmember_pc_type = $obj->account_type;

                        $domainData = $obj->get_domain_detail();
                        $subdomain_url = $domainData['subdomain_url'];
                        $client_details = $obj->get_client_detail();
                        $person_email = $client_details['person_email'];
                        
                        if (strpos($_SERVER['SERVER_NAME'], 'mutualfundpartner.com') !== false) {
                            ?>
                            <div class="col-lg-12 col-md-12 align-self-start">
                                </br>
                                </br>
                                <h5 class="text-center"> Privacy Policy and Terms & Conditions</h5> 
                                <p>
                                    For the purposes of this Privacy Policy, the terms “Distributor”, “we”, “us” and “our” refer to Distributor with ARN number <?php echo $obj->arn_no; ?> (“Distributor”) and/or our subsidiaries and affiliates (if any). “You” or “your” or “Investor” or “Customer” refers to you, an investor and/or customer (existing or prospective), as a user of this Site (defined below). 
                                </p><p>Any natural person who visits <?php echo $_SERVER['HTTP_HOST'] ?> (the "Site") and whose information is collected, received, possessed, stored, dealt in and/or handled by the Distributor who are distributors of asset management companies (“AMCs”) or mutual funds, shall be covered by this Privacy Policy.

                                </p><p>We as Distributors respect your right to privacy. We recognise that the lawful and correct treatment of personal information is very important to maintain your confidence in us, as Distributors.

                                </p><p>It is our endeavour to ensure that any personal information that we, as Distributors, collect, process, record or use in any way whether it is held on paper, on computer or other electronic media will have appropriate safeguards in place to comply with our obligations of confidentiality and privacy. In this regard, we request you to kindly read this ‘Privacy Policy’ carefully.

                                </p><p>The terms as may be used in this document would be in accordance with the Information Technology Act, 2000 and Information Technology (Reasonable security practices and procedures and sensitive personal data or information) Rules, 2011, as may be amended, re-enacted, replaced, re-titled, from time to time and such other rules/regulations and circulars as may be applicable.

                                </p><p><strong>Interpretation</strong> 
                                </p><p>All references to you/ Customer/ Investor whether masculine or feminine include references to non-individuals also unless repugnant to the context thereof. 
                                </p><p>Any reference to any statute or statutory provision shall be construed as including a reference to any statutory modifications or re-enactment therein from time to time. 
                                </p><p><strong>What personal information do we collect as a Distributor </strong>
                                </p><p>This Privacy Policy describes our policies and procedures for the collection, use and disclosure of your personal information and/or sensitive personal data or information (“Information”) in nature, as per the indicative list given below:
                                </p><p>•	Identity details such as your name, age, gender, etc.;
                                </p><p>•	Contact details such as address, email address, telephone number, etc.; 
                                </p><p>•	Bank account details, change of bank account details or registration of multiple bank accounts etc. through application forms, such as account name, account number, nominee details; 
                                </p><p>•	Payment instrument details at the time of submitting application at the time of investment or as proof for carrying out change of bank account or any other details of debit card obtained (if any); 
                                </p><p>•	Generation and storing password (PIN) in encrypted form based on the request from you under the online mode of investments; 
                                </p><p>•	Demat account details like beneficiary account no. etc.; 
                                </p><p>•	Annual Income and savings profile; 
                                </p><p>•	Technical data about your equipment including computer and mobile device, browsing actions and patterns and online activity;
                                </p><p>•	Location details such as IP address etc.;
                                </p><p>•	Information about you that you give us by filling in forms, surveys, or by communicating with us, whether face-to-face, by phone, email, online, or otherwise; and 
                                </p><p>•	Other information as may be considered as personal information or sensitive personal data or information under applicable law.

                                </p><p><strong>Purpose of collection of Information </strong>
                                </p><p>Your Information shall be used by us for the relevant lawful purposes connected with various functions or activities related to offering you the services of the AMCs/ mutual funds in which you are interested, and/or to help determine your eligibility for the product/services requested/ applied/ shown interest in and/or to enable your verification and/or process applications, requests, transactions from you and/or maintain records as per internal/legal/regulatory requirements and shall be used to provide you with the best possible services/products. 
                                </p><p>Further, your Information is being collected by the us to respond to your requests, to process the investment applications and to also ensure safe subscriptions/redemptions through points of acceptance in physical form, website, online portals, and such other mode. 

                                </p><p>We may also use your Information to call you to inform you with respect to the other products or services offered by the us. The said Information is also being collected to be compliant with the Know Your Client (KYC) norms under the Prevention of Money Laundering Act, 2002, (PMLA), the rules issued thereunder, and guidelines and circular on Anti- Money Laundering issued by SEBI and SEBI (Mutual Funds) Regulations, 1996 as amended till date. 

                                </p><p><strong>How do we use your Information </strong>
                                </p><p>The aforesaid information is the basis of our ability to provide you with good, qualitative, timely and efficient service, while also keeping your Information provided totally secure and confidential. 

                                </p><p>We endeavour to have suitable technical, operational and physical security controls and measures to protect your Information or information that are commensurate with the nature of our business at all times.

                                </p><p>We permit only authorized employees who are trained in the proper handling of customer information, to have access to your Information. We would ensure to enter into suitable terms with third party service providers in order to protect client confidentiality and follow a non-disclosure or restricted disclosure policy. Further, the employees who violate the privacy and confidentiality promise shall be subjected to disciplinary action.

                                </p><p><strong>Who do we share your Information with? </strong>
                                </p><p>We may share your Information with the AMCs/ mutual funds where we are distributors. The said AMCs/ mutual funds may also share your Information, without obtaining your prior written consent, to provide you with the products and services that you have applied for. Further, it may also share your Information with government agencies mandated under the law to obtain information for the purpose of verification of identity, or for prevention, detection, investigation including cyber incidents, prosecution, and punishment of offences, or where disclosure is necessary for compliance by the said AMCs/ mutual funds of a legal obligation and may be required to disclose any of your Information to any third party by an order under the law for the time being in force.

                                </p><p>Further, we shall reveal only such Information to external authorities as may be found relevant and necessary in order to comply with the applicable laws of the land and to third party service providers to ensure smooth functioning of the activities as may be prescribed under applicable law like Registrar and Transfer Agents/banks/sub-brokers/call centers/custodians/depositories and such other entities/persons. By using our website or by agreeing to transact with us, you agree to the above sharing of Information during your relationship with us. We may also be required from time to time to disclose your Information to governmental or judicial bodies or agencies or our regulators based on their requirement to comply with the applicable laws including cyber laws. In addition to the above we may also use the Information shared by you to contact your regarding the products and services offered by us and seek feedback on the services provided. Further, it may be noted that Internet Protocol (IP) addresses, browser supplied information and site session information may be recorded as part of the normal operations and for security monitoring purposes. 
                                </p><p>Nevertheless, it is our foremost endeavour to ensure that the your privacy is protected at all points in time. 

                                </p><p><strong>Correct Information</strong>
                                </p><p>Please note that the accuracy of the Information provided to us is essential. It is therefore a term and condition governing the access and use of this Site that you undertake to ensure the accuracy and completeness of all Information disclosed, shared or exchanged. In case if any of the data/Information is found to be inaccurate or incorrect, the same shall be informed to us for immediate amendment or updation via e-mail at <?php echo $person_email; ?>. We and any person acting on our behalf shall not be responsible for the authenticity of the Information supplied by you.

                                </p><p><strong>Cookies</strong> 
                                </p><p>Our website may use cookies. By using our website and agreeing to these terms of use, you consent to our use of cookies in accordance with the terms of our privacy policy.

                                </p><p><strong>Third party links</strong>
                                </p><p>Please note that you shall not be covered by the terms of this Policy if you access websites, services or applications operated by any third parties or any Information that you submit through such third party websites, services or application (except where such Information is shared with us by those third parties).  

                                </p><p><strong>Amendments/Modifications</strong>
                                </p><p>This Privacy Policy available on our website is current and valid. However, we reserve the right to amend/modify any of the sections of this policy at any time and you are requested to keep themselves updated for changes by reading the same from time to time. 

                                </p><p><strong>Retention of Information</strong>
                                </p><p>It may be noted that we may retain the data as may be provided by you till such time as may be provided under the law and as required to efficiently provide service to you. 
                                </p><p><strong>Important Disclaimer </strong>
                                </p><p>You understand that the Site is being made available to you independently and directly by us i.e. the Distributor and that the aforementioned AMCs/ mutual funds have no role in the same. We are solely responsible for any access, use and/or processing of your Information as governed by this Privacy Policy and only we are responsible for any Information shared by the you or accessed or used by the Distributor i.e. us or any other party and we shall be solely liable for the same.
                                </p><p>Further, despite our efforts to protect your privacy, if unauthorized persons breach security control measures and illegally use such Information the AMCs/ mutual funds (or their representatives) where we are distributors shall not be held responsible/liable.
                                </p>
                                <p><strong>Grievance redressal</strong></p><p> 
                                    If you have any further queries, complaints and/or concerns related to privacy or to the processing of your Information, they may be addressed to <?php echo $person_email; ?>.  </p> 
                                <p><strong>Disclaimer</strong></p><p> 
                                    Before agreeing to share any information, data or any other details on this website/webpage (“Site”), the visitor (“Visitor”) to please note that this is a Site of the distributor (“Distributor”) who is empanelled and registered with HDFC Asset Management Company Limited (“AMC”)/HDFC Mutual Fund (“MF”)/ HDFC Trustee Company Limited (“Trustee”) and is not a Site of the AMC/ MF/ Trustee. Please also note that this Site is being hosted by a third-party provider viz. Bizight Solutions Private Limited (“Bizight”). The Privacy Policy of the respective Distributor as given on this Site shall apply to the Visitor. </p>
                                
                                
                                <p> <strong>Terms & Conditions</strong> </p>
								
                                <p>Before agreeing to share any information, data or any other details on this website/webpage (“<strong>Site</strong>”), the visitor (“<strong>Visitor</strong>”) to please note that this is a Site of the distributor (“<strong>Distributor</strong>”) who is empanelled and registered with HDFC Asset Management Company Limited (“<strong>AMC</strong>”)/HDFC Mutual Fund (“<strong>MF</strong>”)/ HDFC Trustee Company Limited (“<strong>Trustee</strong>”) and is not a Site of the AMC/ MF/ Trustee. Please also note that this Site is being hosted by a third-party provider viz. Bizight Solutions Private Limited (“<strong>Bizight</strong>”). The Privacy Policy of the respective Distributor as given on this Site shall apply to the Visitor. </p>
                                </br>It shall be at the sole discretion of the Visitor to avail of the services being offered by the Distributor on this Site and the Visitor may choose not to avail of the same. In case of any queries/complaints/grievances including with regard to the products/services being provided, the Visitor may contact the Distributor directly and the AMC/ MF/ Trustee shall not be responsible or liable for any misrepresentation or fraud or any compromise of the Visitor’s information by the Distributor or Bizight. Please note that all electronic medium including this Site could be susceptible to security breach, data theft, frauds, system failures, none of which shall be the responsibility of the AMC/ MF/ Trustee. The interactions, requests, transactions and services availed by the Visitor shall be at his/her own risk and assessment. 
                                </br>Mutual Fund investments are subject to market risk and are governed by the terms of the scheme related documents. The Visitor should consult his/her legal/financial/tax advisors before making any investment decisions. Further the AMC /MF/ Trustee and their representatives, accept no responsibility of contents which are incorporated by the Distributor for the convenience of the Visitor. 
								<p>By visiting this Site, the Visitor hereby confirms that he agrees and accepts the above Terms and Conditions applicable to him/her.</p>




                            </div>               
    <?php
} else if (strpos($_SERVER['SERVER_NAME'], 'nimfpartners.com') !== false) {
    ?>

                            <div class="col-lg-12 col-md-12 align-self-start">

                                <h5 class="text-center"> Privacy Policy and Terms & Conditions</h5> 

                                <p>
                                    For the purposes of this Privacy Policy, the terms “” “Distributor”, “we”, “us” and “our” refer to having its address at (“Distributor”) and/or our subsidiaries and affiliates (if any). “You” or “your” or “Investor” or “Customer” refers to you, an investor and/or customer (existing or prospective), as a user of this Site (defined below). 
                                </p><p>Any natural person who visits <strong><?php echo $_SERVER['HTTP_HOST'] ?> </strong>(the "Site") and whose information is collected, received, possessed, stored, dealt in and/or handled by the Distributor who are distributors of asset management companies (“AMCs”) or mutual funds, shall be covered by this Privacy Policy. 
                                </p><p>We as Distributors respect your right to privacy. We recognise that the lawful and correct treatment of personal information is very important to maintain your confidence in us, as Distributors. 
                                </p><p>It is our endeavour to ensure that any personal information that we, as Distributors, collect, process, record or use in any way whether it is held on paper, on computer or other electronic media will have appropriate safeguards in place to comply with our obligations of confidentiality and privacy. In this regard, we request you to kindly read this ‘Privacy Policy’ carefully. 
                                </p><p>The terms as may be used in this document would be in accordance with the Information Technology Act, 2000 and Information Technology (Reasonable security practices and procedures and sensitive personal data or information) Rules, 2011, as may be amended, re-enacted, replaced, re-titled, from time to time and such other rules/regulations and circulars as may be applicable. 
                                </p><p><strong>Interpretation </strong>
                                </p><p>All references to you/ Customer/ Investor whether masculine or feminine include references to non-individuals also unless repugnant to the context thereof. 
                                </p><p>Any reference to any statute or statutory provision shall be construed as including a reference to any statutory modifications or re-enactment therein from time to time. 
                                </p><p><strong>What personal information do we collect as a Distributor </strong>
                                </p><p>This Privacy Policy describes our policies and procedures for the collection, use and disclosure of your personal information and/or sensitive personal data or information (“Information”) in nature, as per the indicative list given below: 
                                </p><p>• Identity details such as your name, age, gender, etc.; 
                                </p><p>• Contact details such as address, email address, telephone number, etc.; 
                                </p><p>• Bank account details, change of bank account details or registration of multiple bank accounts etc. through application forms, such as account name, account number, nominee details; 
                                </p><p>• Payment instrument details at the time of submitting application at the time of investment or as proof for carrying out change of bank account or any other details of debit card obtained (if any); 
                                </p><p>• Generation and storing password (PIN) in encrypted form based on the request from you under the online mode of investments; 
                                </p><p>• Demat account details like beneficiary account no. etc.; 
                                </p><p>• Annual Income and savings profile; 
                                </p><p>• Technical data about your equipment including computer and mobile device, browsing actions and patterns and online activity; 
                                </p><p>• Location details such as IP address etc.; 
                                </p><p>• Information about you that you give us by filling in forms, surveys, or by communicating with us, whether face-to-face, by phone, email, online, or otherwise; and 
                                </p><p>• Other information as may be considered as personal information or sensitive personal data or information under applicable law. 
                                </p><p><strong>Purpose of collection of Information </strong>
                                    Your Information shall be used by us for the relevant lawful purposes connected with various functions or activities related to offering you the services of the AMCs/ mutual funds in which you are interested, and/or to help determine your eligibility for the product/services requested/ applied/ shown interest in and/or to enable your verification and/or process applications, requests, transactions from you and/or maintain records as per internal/legal/regulatory requirements and shall be used to provide you with the best possible services/products. 
                                </p><p>Further, your Information is being collected by us to respond to your requests or to process the investment applications and to also ensure safe subscriptions/redemptions through points of acceptance in physical form, website, online portals, and such other mode. 
                                </p><p>We may also use your Information to call you to inform with respect to the other products or services offered by us. The said Information is also being collected to be compliant with the Know Your Client (KYC) norms under the Prevention of Money Laundering Act, 2002, (PMLA), the rules issued thereunder, and guidelines and circular on Anti- Money Laundering issued by SEBI under SEBI (Mutual Funds) Regulations, 1996 as amended till date. 
                                </p><p><strong>How do we use your Information </strong>
                                </p><p>The aforesaid information is the basis of our ability to provide you with good, qualitative, timely and efficient service, while also keeping your Information provided totally secure and confidential. 
                                </p><p>We endeavour to have suitable technical, operational and physical security controls and measures to protect your Information or information that are commensurate with the nature of our business at all times. 
                                </p><p>We permit only authorized employees who are trained in the proper handling of customer information, to have access to your Information. We would ensure to enter into suitable terms with third party service providers in order to protect client confidentiality and follow a non-disclosure or restricted disclosure policy. Further, the employees who violate the privacy and confidentiality promise shall be subjected to disciplinary action. 
                                </p><p><strong>Who do we share your Information with? </strong>
                                </p><p>We may share your Information with the AMCs/ mutual funds where we are distributors. The said AMCs/ mutual funds may also share your Information, without obtaining your prior written consent, to provide you with the products and services that you have applied for. Further, it may also share your Information with government agencies mandated under the law to obtain information for the purpose of verification of identity, or for prevention, detection, investigation including cyber incidents, prosecution, and punishment of offences, or where disclosure is necessary for compliance by the said AMCs/ mutual funds of a legal obligation and may be required to disclose any of your Information to any third party by an order under the law for the time being in force. Further, we shall reveal only such Information to external authorities as may be found relevant and necessary in order to comply with the applicable laws of the land and to third party service providers to ensure smooth functioning of the activities as may be prescribed under applicable law like Registrar and Transfer Agents/banks/sub-brokers/call centers/custodians/depositories and such other entities/persons. By using our website or by agreeing to transact with us, you agree to the above sharing of Information during your relationship with us. We may also be required from time to time to disclose your Information to governmental or judicial bodies or agencies or our regulators based on their requirement to comply with the applicable laws including cyber laws. In addition to the above we may also use the Information shared by you to contact your regarding the products and services offered by us and seek feedback on the services provided. Further, it may be noted that Internet Protocol (IP) addresses, browser supplied information and site session information may be recorded as part of the normal operations and for security monitoring purposes. 
                                </p><p>Nevertheless, it is our foremost endeavour to ensure that your privacy is protected at all points in time. 
                                </p><p><strong>Correct Information </strong>
                                </p><p>Please note that the accuracy of the Information provided to us is essential. It is therefore a term and condition governing the access and use of this Site that you undertake to ensure the accuracy and completeness of all Information disclosed, shared or exchanged. In case if any of the data/Information is found to be inaccurate or incorrect, the same shall be informed to us for immediate amendment or updation via e-mail at . We and any person acting on our behalf shall not be responsible for the authenticity of the Information supplied by you. 
                                </p><p><strong> Cookies </strong>
                                </p><p>Our website may use cookies. By using our website and agreeing to these terms of use, you consent to our use of cookies in accordance with the terms of our privacy policy. 
                                </p><p><strong>Third party links </strong>
                                </p><p>Please note that you shall not be covered by the terms of this Policy if you access websites, services or applications operated by any third parties or any Information that you submit through such third party websites, services or application (except where such Information is shared with us by those third parties). 
                                </p><p><strong>Amendments/Modifications </strong>
                                </p><p>This Privacy Policy available on our website is current and valid. However, we reserve the right to amend/modify any of the sections of this policy at any time and you are requested to keep themselves updated for changes by reading the same from time to time. 
                                </p><p><strong>Retention of Information </strong>
                                </p><p>It may be noted that we may retain the data as may be provided by you till such time as may be provided under the law and as required to efficiently provide service to you. 
                                </p><p><strong>Important Disclaimer </strong>
                                </p><p><strong>RISK FACTORS:</strong>
                                </p><p><strong>Mutual Fund investments are subject to market risks, read all scheme related documents carefully.</strong>
                                </p><p>Further, despite our efforts to protect your privacy, if unauthorized persons breach security control measures and illegally use such Information the AMCs/ mutual funds (or their representatives) where we are distributors shall not be held responsible/liable. 
                                </p><p>Grievance redressal If you have any further queries, complaints and/or concerns related to privacy or to the processing of your Information, they may be addressed to. 
                                </p><p><strong>Terms & Conditions </strong>
                                </p><p><strong>(“Site”), the visitor (“Visitor”) to please note that this is a Site of the distributor (“Distributor”) who is empanelled and registered with Nippon Life India Asset Management Limited (“AMC”) / Nippon India Mutual Fund (“MF”)/ Nippon Life India Trustee Limited (“Trustee”) and is not a Site of the AMC/ MF/ Trustee. Please also note that this Site is being hosted by a third-party provider viz. Bizight Solutions Private Limited (“Bizight”). The Privacy Policy of the respective Distributor as given on this Site shall apply to the Visitor. </strong>

                                </p><p><strong>It shall be at the sole discretion of the Visitor to avail of the services being offered by the Distributor on this Site and the Visitor may choose not to avail of the same. In case of any queries/complaints/grievances including with regard to the products/services being provided, the Visitor may contact the Distributor directly and the AMC/ MF/ Trustee shall not be responsible or liable for any misrepresentation or fraud or any compromise of the Visitor’s information by the Distributor or Bizight. Please note that all electronic medium including this Site could be susceptible to security breach, data theft, frauds, system failures, none of which shall be the responsibility of the AMC/ MF/ Trustee. The interactions, requests, transactions and services availed by the Visitor shall be at his/her own risk and assessment.</strong> 
                                </p><p><strong>Mutual Fund investments are subject to market risk and are governed by the terms of the scheme related documents. The Visitor should consult his/her legal/financial/tax advisors/ Mutual Fund Distributors before making any investment decisions. </strong>


                            </div>
<?php } else if (strpos($_SERVER['SERVER_NAME'], 'maxlifeinsurance.agency') !== false) {
    ?>
                            <div class="col-lg-12 col-md-12 align-self-start">

                                <h5 class="text-center"> Privacy Policy</h5> 

                                <p>We at SalesPanda (Bizight Solutions Pvt LTD.) are committed to protecting your privacy. This
                                    Product Privacy Policy applies to your use of the SalesPanda Subscription Service as a
                                    customer of SalesPanda. This Product Privacy Policy describes how we collect, receive, use,
                                    store, share, transfer, and process your Personal Data. It also describes your choices regarding
                                    use, as well as your rights of access and correction of your Personal Data.
                                </p><p>This Product Privacy Policy also describes how we process Customer Data on behalf of our
                                    customers in connection with the SalesPanda Subscription Services. This Product Privacy
                                    Policy does not apply to any information or data collected by SalesPanda as a controller for
                                    other purposes, such as information collected on our websites or through other channels for
                                    marketing purposes.
                                </p><p>Bizight Solutions Private Limited has built the MAX LIFE I AM THE DIFFERENCE as organizational app and
                                    is a Private app for internal use of Max Life Insurance Company Limited.
                                </p><p>1. Use of the Subscription Service

                                </p><p>A. The SalesPanda Subscription Service
                                </p><p>Our online Subscription Service allows users to create and share marketing, sales
                                    and customer service content. The Subscription Service can also be used to help
                                    organize channel sales data about a company’s sales pipeline (e.g., leads,
                                    customers, deals, etc.). The information added to the Subscription Service, either
                                    by site visitors providing their contact information or when a Subscription
                                    Service user adds the information, is stored and managed on our service
                                    providers&#39; servers. SalesPanda provides the Subscription Service to our
                                    customers for their own marketing, sales, CRM, and customer service needs.
                                </p><p>B. Use By Our Customers
                                </p><p>Our customers use the Subscription Service to build Microsites that people can
                                    visit to learn more about their business. When customers use the Subscription
                                    Service, they may collect Personal Data such as first and last name, email
                                    address, physical address, or phone number. SalesPanda does not control the
                                    content of these Microsites or the types of Personal Data that our customers
                                    may choose to collect or manage using the Subscription Service. That Personal
                                    Data is controlled by them and is used, disclosed and protected by them
                                    according to their privacy policies. SalesPanda processes our customers&#39;
                                    information as they direct and in accordance with our agreements with our
                                    customers, and we store it on our service providers&#39; servers.
                                </p><p>Our agreements with our customers prohibit us from using that information,
                                    except as necessary to provide and improve the Subscription Service, as
                                    permitted by this Product Privacy Policy, and as required by law. We have no
                                    direct relationship with individuals who provide Personal Information to our
                                    customers. Our customers control and are responsible for correcting, deleting or
                                    updating information they have collected from using the Subscription Service.
                                </p><p>
                                </p><p>We may work with our customers to help them provide notice to their visitors
                                    about their data collection, processing and usage.
                                </p><p>Children’s Privacy
                                </p><p>
                                </p><p>
                                    These Services do not address anyone under the age of 13. We do not knowingly collect
                                    personally identifiable information from children under 13. In the case we discover that
                                    a child under 13 has provided us with personal information, we immediately delete this
                                    from our servers. If you are a parent or guardian and you are aware that your child has
                                    provided us with personal information, please contact us so that we will be able to do
                                    necessary actions.
                                </p><p>    

                                </p><p>2. SalesPanda Product Specific Privacy Disclosures
                                </p><p>A. All Product Tiers
                                </p><p>i. Third Parties
                                </p><p>We may provide links within our sites and services to the sites or services
                                    of third parties. We are not responsible for the collection, use,
                                    monitoring, storage or sharing of any Personal Data by such third parties,
                                    and we encourage you to review those third parties&#39; privacy notices and
                                    ask them questions about their privacy practices as they relate to you.
                                </p><p>ii. Google Integrations
                                </p><p>If you choose to integrate your Gmail with the Subscription Service you
                                    may use the following integrations and allow SalesPanda access to your
                                    Google user data:
                                </p><p>(1) Gmail Integration
                                </p><p>By using the ‘Gmail Integration’ with the Subscription Service you will
                                    grant the Subscription Service access to information associated with your
                                    account, including contacts, calendar.
                                </p><p>(2) Google Calendar Integration
                                </p><p>The Subscription Service will have access to both your Google Calendar
                                    and any other calendar you access via Google in order to power our Task
                                    Module, and allow you to associate events with contacts. The
                                    Subscription Service will have the ability to: create or change your
                                    calendars, and update individual calendar events.
                                </p><p>iii. Data Practices and Service Data
                                </p><p>We automatically collect metrics and information about how Users
                                    interact with and use the Subscription Service. We use this information to
                                    develop and improve the Subscription Services and the Consulting
                                    Services, and to inform our sales and marketing strategies. We may share
                                    or publish this service data with third parties in an aggregated and
                                    anonymized manner, but we will not include any Customer Data or
                                    identify Users.
                                </p><p>
                                </p><p>
                                    If you access the Subscription Services via our mobile applications, we
                                    may also collect your device model and version, device identifier, and OS
                                    version. We may send you push notifications from time to time in order
                                    to update you about events or promotions. If you no longer wish to
                                    receive such communications, you may turn them off at the device level.
                                    When you use the Subscription Service, we automatically collect log files.
                                    These log files contain information about a Users’ IT system, a User’s IP
                                    address, browser type, domain names, internet service provider (ISP), the
                                    files viewed on our site (e.g., HTML pages, graphics, etc.), operating
                                    system, clickstream data, access times, and referring website addresses.
                                    We use this information to ensure the optimal operation of the
                                    Subscription Service and for security purposes. We may link log files to
                                    Personal Data such as name, email address, address, and phone number
                                    for these purposes.
                                </p><p>iv. Integrations with the SalesPanda Platform
                                </p><p>You may choose to connect any number of applications or integrations,
                                    including our certified partner applications, with your SalesPanda
                                    account. If you give an integration provider access to your SalesPanda
                                    account then your use of these integrations is subject to the service
                                    terms and privacy terms made available by that integrator. We are not
                                    responsible for third party integrators and in no case are such integration
                                    providers our sub-processors.
                                </p><p>A . Twitter Integration
                                </p><p>You may choose to integrate your Twitter account with the SalesPanda
                                    Platform (depending on product tier) in order to manage your Twitter. As
                                    part of posting Tweets through the Subscription Service, the SalesPanda
                                    platform will store your Tweets and post Tweets upon their scheduled
                                    time as selected by you. Additionally, the Subscription Service will add
                                    tracking code to any Tweet URL generated through the SalesPanda
                                    Platform, solely for the purpose of tracking clicks. The Subscription
                                    Service will store replies to and analytics for the
                                    performance of your Tweets.
                                </p><p>B. LinkedIn Integrations
                                </p><p>Your use of the LinkedIn Ads integration is also subject to the terms and
                                    conditions provided by LinkedIn, available at:
                                </p><p>https://www.linkedin.com/legal/sas-terms.
                                    If you choose to connect your LinkedIn account with our Social Tool
                                    (subject to your product tier), as part of posting to LinkedIn through the
                                </p><p>Subscription Service, the SalesPanda platform will store your posts and
                                    publish only at the time you schedule. Additionally, the Subscription
                                    Service will add tracking code to any post URL generated through the
                                    SalesPanda Platform, solely for the purpose of tracking clicks. The
                                </p><p>
                                    Subscription Service stores comments and replies on your posts, as well
                                    as analytics for the performance of your posts. As part of any LinkedIn
                                    integration the Subscription Service will store your account name and
                                    profile picture.
                                </p><p>C. Facebook
                                </p><p>If you choose to connect your Facebook account with our Social Tool
                                    (subject to your product
                                    tier), as part of posting to Facebook and/or Instagram through the
                                    Subscription Service, the SalesPanda platform will store your posts and
                                    publish only at the time you schedule.
                                </p><p>Additionally, the Subscription Service will add tracking code to any post
                                    URL generated through the SalesPanda Platform, solely for the purpose
                                    of tracking clicks. The Subscription Service stores comments and replies
                                    on your posts, as well as analytics for the performance of your posts
                                    You may request the deletion of your SalesPanda account or Subscription
                                    Service by sending a request on support@salespanda.com

                                </p><p>5. Data Retention
                                    Customer Data collected during your use of the Subscription Service is retained.
                                    Your data is deleted upon your written request or after an established period
                                    following the termination of all customer agreements. In general, Customer Data is
                                    deleted after your paid Subscription ends and your portal becomes inactive. If you
                                    have an unresolved privacy or data use concern that we have not addressed
                                    satisfactorily, please contact us.
                                </p><p>6. Confidentiality
                                    Sales Panda shall take all reasonable precautions to preserve the confidentiality and
                                    prevent any corruption or loss, damage or destruction of the data and information
                                    provided by you, in keeping with industry standard practices.
                                </p><p>
                                    Password: Certain services on the website/mobile application may require authentication
                                    procedures. You would be able to access such services by using the user ID and the
                                    password. Sales Panda shall take reasonable care to ensure the security of and to prevent
                                    unauthorized access to the services, which are part of the website/mobile application.
                                    Contact
                                </p><p>
                                    Data Protection Officer (DPO)
                                </p><p>
                                    Akanksha Mishra
                                </p><p>
                                    Akanksha.mishra@salespanda.com
                                </p><p>
                                    SalesPanda, c/o Bizight Solutions Pvt Limited
                                </p><p>
                                    105, Chiranjiv Tower, Nehru Place, Delhi 110019
                                </p><p>

                            </div>

    <?php }
?>

                    </div>
                </div>
            </section>


            <!-- === contact-us END=== -->

        </div>
        <!-- === Main Content End === --></div></div>
</div>
</div>
</div>

</body></html>


