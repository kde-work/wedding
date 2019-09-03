
generatedGuidFullLists = {};

function generateGuid() {
    var result, i, j;

    do
    {
        result = '';
        for (j = 0; j < 32; j++) {
            if (j == 8 || j == 12 || j == 16 || j == 20)
                result = result + '-';
            i = Math.floor(Math.random() * 16).toString(16).toUpperCase();
            result = result + i;
        }

        // TODO log this situation
        if (result in generatedGuidFullLists) {
            console.log('Generated GUID is not unique!!!!');
            alert('Generated GUID is not unique!!!!');
        }

    } while (result in generatedGuidFullLists);

    generatedGuidFullLists[result] = 1;
    
    return result;
}

// Math functions
function sinDeg(num) { return Math.sin(num / 180 * Math.PI); };
function cosDeg(num) { return Math.cos(num / 180 * Math.PI); };

// string functions
function IsNullOrEmpty(s) {
    if ((s == null) || (s == ""))
        return true;
    else
        return false;
}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function ConvertIntToYesNo(val) {
    if (val == 0)
        return "Нет";
    else
        return "Да";
}