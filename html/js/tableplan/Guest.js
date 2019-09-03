
// Possible Guest types
// 0 - not set
// 1 - Male
// 2 - Female
// 3 - Boy
// 4 - Girl
// 5 - Infant
// 6 - VIP

// Guest class
function Guest(guestID, guestName, guestType, guestRSVP, guestMeal, guestTable, guestSeat) {
    if (guestID == null)
        this.GuestID = generateGuid();
    else
        this.GuestID = guestID;

    this.GuestName = guestName;
    this.GuestType = guestType;
    this.GuestRSVP = guestRSVP;
    this.GuestMeal = guestMeal;
    this.GuestTable = guestTable;
    this.GuestSeat = guestSeat;

    this.IsUnseated = function () {
        if (IsNullOrEmpty(this.GuestTable) || IsNullOrEmpty(this.GuestSeat))
            return true;
        else
            return false;
    }
}

function GuestType2String(type) {
    switch (parseInt(type)) {
        case 1: return "M.";
        case 2: return "F.";
        case 3: return "Boys";
        case 4: return "Girls";
        case 5: return "Млад.";
        case 6: return "VIP";
        case 0:
        default:
            return "-----";
    }

    return "-----";
}

function GuestRSVP2String(rsvp) {
    switch (parseInt(rsvp)) {
        case 1: return "Yes";
        case 2: return "No";
        case 3: return "Thinks";
        case 0:
        default:
            return "-----";
    }

    return "-----";
}