/*
 * @validateForm
 * Form Validating Script
 */
function validateForm(formObj) {
    var _frmData = [];
    var _err = false;
    _frmData.Obj = jQuery(formObj);

    jQuery(formObj).find('.required').each(function () {
        _frmData.tag = jQuery(this).prop('tagName');
        _frmData.tagName = jQuery(this).attr('name');
        _frmData.tagError = jQuery(this).attr('error-message');
        _frmData.tagValue = jQuery(this).val().trim();

        switch (_frmData.tagName) {
            case "umobile":
                if (_frmData.tagValue == '' || isNaN(_frmData.tagValue) || _frmData.tagValue.length != 10) {
                    showMessage(_frmData.tag, _frmData.tagName, _frmData.tagError);
                    _err = true;
                }
                if (_frmData.tagValue != '' && !validateMobile(_frmData.tagValue)) {
                    showMessage(_frmData.tag, _frmData.tagName, 'Please enter valid mobile number');
                    _err = true;
                }
                break;

            case "ufname":
            case "ulname":
                var _errMsg = '';
                if (_frmData.tagValue == '') {
                    showMessage(_frmData.tag, _frmData.tagName, _frmData.tagError);
                    _err = true;
                } else if (_frmData.tagValue.length <= 2) {
                    _errMsg = 'Please enter minimum 3 characters';
                    showMessage(_frmData.tag, _frmData.tagName, _errMsg);
                    _err = true;
                } else if (_frmData.tagValue.length > 2) {
                    var pattern = /^[a-zA-Z- ]*$/;
                    if (!pattern.test(_frmData.tagValue)) {
                        _errMsg = 'Please only use standard alphabets';
                        showMessage(_frmData.tag, _frmData.tagName, _errMsg);
                        _err = true;
                    }
                } else {
                    if (_frmData.tagName == "ulname" || _frmData.tagName == "ufname") {
                        jQuery('#uemail').focus();
                    }
                }
                break;

            case "uemail":
                if (_frmData.tagValue == '') {
                    showMessage(_frmData.tag, _frmData.tagName, _frmData.tagError);
                    _err = true;
                } else if (_frmData.tagValue != '') {
                    if (!isEmail(_frmData.tagValue)) {
                        _errMsg = 'Please enter valid email';
                        showMessage(_frmData.tag, _frmData.tagName, _errMsg);
                        _err = true;
                    }
                }
                break;
            
            case "password":
            case "cpassword":
                var _errMsg = '';
                if (_frmData.tagValue == '') {
                    showMessage(_frmData.tag, _frmData.tagName, _frmData.tagError);
                    _err = true;
                } else if (_frmData.tagValue.length < 8) {
                    _errMsg = 'Please enter minimum 8 characters';
                    showMessage(_frmData.tag, _frmData.tagName, _errMsg);
                    _err = true;                    
                }else{
                    /** MATCH PASSWORDS */
                    var pwd = jQuery('input[name="password"').val();
                    var cpwd = jQuery('input[name="cpassword"').val();
                    if (pwd != '' && cpwd != '' && pwd != cpwd) {
                        _errMsg = 'Please match both the Passwords';
                        showMessage(_frmData.tag, _frmData.tagName, _errMsg);
                        _err = true;
                    }
                }
                break;

            default:
                if (_frmData.tagValue == '') {
                    showMessage(_frmData.tag, _frmData.tagName, _frmData.tagError);
                    _err = true;
                }
                break;
        }
        if (_err) {
            return false;
        }
    });

    if (_err) {
        jQuery("input[name='" + _frmData.tagName + "']").focus();
        return false;
    } else {
        //jQuery("input[name='"+_frmData.tagName).focus();
        return true;
    }
}

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

/*
 * @showMessage
 * Error Message Handling Script
 */
function showMessage(tag, tagName, tagError) {
    var _errMsg = '<span class="text-error errorMsg">' + tagError + '</span>';
    var _iptFld = tag+'[name="' + tagName + '"]';

    jQuery('span.errorMsg').remove();
    jQuery(_errMsg).insertAfter(_iptFld).delay(10000).fadeOut(1500);
    return;
}

var siteMode = '';
if((navigator.userAgent).match('Mobile') && !navigator.userAgent.match(/iPad/i)){
    siteMode = 'mobile';
}else if(navigator.userAgent.match(/iPad/i)){
    siteMode = 'ipad';
}else{
    siteMode = 'desktop';
}

/*
 * show form
 */
function requestOfferForm(_id, type, reqst) {
    jQuery.ajax({
        url: _ajax_UrL,
        data: {reqst: reqst, action: 'call_request_form', id: _id, typ: type},
        method: 'POST',
        dataType: 'JSON',
        cache: false,
        beforeSend: function () {
            /*jQuery("#loading-image").attr('src', _srcLoadImg).show();*/
            jQuery('#popUpForm').fadeIn(300);
        },
        success: function (res) {
            /*jQuery("#loading-image").hide();*/
        }
    });
}

function validateMobile(v) {
    // var timePat ="^((\+){0,1}91(\s){0,1}(\-){0,1}(\s){0,1}){0,1}9[0-9](\s){0,1}(\-){0,1}(\s){0,1}[1-9]{1}[0-9]{7}$";
    // var matchArray = v.match(timePat);
    var pattern = /^\d{10}$/;
    if (!pattern.test(v)) {
        return false;
    } else if (v.length > 0) {
        if (v.length != 10) {
            return false;
        } else if (v[0] == 0 || v[0] == 1) {
            return false;
        } else if (v == "1234567890") {
            return false;
        } else if (v == "9999999999") {
            return false;
        } else if (v == "8888888888") {
            return false;
        } else if (v == "7777777777") {
            return false;
        } else if (v == "6666666666") {
            return false;
        } else if (v == "5555555555") {
            return false;
        } else if (v == "4444444444") {
            return false;
        } else if (v == "3333333333") {
            return false;
        } else if (v == "2222222222") {
            return false;
        } else if (v == "1111111111") {
            return false;
        } else if (v == "0000000000") {
            return false;
        } else if (v == "9876543210") {
            return false;
        } else if (v == "9000000000") {
            return false;
        } else if (v == "9898989898") {
            return false;
        } else {
            return true;
        }
    }
}