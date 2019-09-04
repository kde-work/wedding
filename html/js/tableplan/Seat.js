"use strict";
/**
 * @param {!Object} value
 * @param {!Object} i
 * @param {number} data
 * @param {number} linkedEntities
 * @param {?} force
 * @return {?}
 */
function Seat(value, i, data, linkedEntities, force) {
    var elem = new Kinetic.Group({
        x: data,
        y: linkedEntities
    });
    elem["SeatObject"] = new Kinetic.Circle({
        x: 0,
        y: 0,
        radius: GetSeatSizeByGuestType(i) / 2,
        fill: GetSeatColorByGuestType(i),
        stroke: "gray",
        strokeWidth: 2
    });
    elem["GuestNameObject"] = new Kinetic.Text({
        x: 0,
        y: 0,
        text: "",
        fontSize: 13,
        fontFamily: "Calibri",
        fill: "black",
        align: "center"
    });
    elem["add"](elem.SeatObject);
    elem["add"](elem.GuestNameObject);
    if (value == null) {
        elem["SeatID"] = generateGuid();
    } else {
        /** @type {!Object} */
        elem["SeatID"] = value;
    }
    /** @type {!Object} */
    elem["GuestID"] = i;
    elem["TableID"] = force;
    /** @type {boolean} */
    elem["ShowColor"] = true;
    /**
     * @return {?}
     */
    elem["IsFree"] = function() {
        if (this["GuestID"] == null || this["GuestID"] == "") {
            return true;
        } else {
            return false;
        }
    };
    /** @type {boolean} */
    elem["IsSelected"] = false;
    /**
     * @param {?} canCreateDiscussions
     * @return {undefined}
     */
    elem["SelectSeat"] = function(canCreateDiscussions) {
        this["IsSelected"] = canCreateDiscussions;
        if (this["IsSelected"]) {
            this["SeatObject"]["setStroke"]("red");
        } else {
            this["SeatObject"]["setStroke"]("gray");
        }
    };
    /**
     * @param {!Object} X_Foobar
     * @return {undefined}
     */
    elem["SetGuest"] = function(X_Foobar) {
        if (X_Foobar != null) {
            var newText = tablePlan["Guests"][X_Foobar]["GuestName"]["replace"](
                /[ ]+/,
                ""
            );
            this["GuestNameObject"]["setText"](newText);
        } else {
            this["GuestNameObject"]["setText"]("");
        }
        this["GuestNameObject"]["setOffset"]({
            x: this["GuestNameObject"]["getWidth"]() / 2,
            y: this["GuestNameObject"]["getHeight"]() / 2
        });
        this["SeatObject"]["setRadius"](GetSeatSizeByGuestType(X_Foobar) / 2);
        if (this["ShowColor"]) {
            this["SeatObject"]["setFill"](GetSeatColorByGuestType(X_Foobar));
        }
    };
    /**
     * @param {?} canCreateDiscussions
     * @return {undefined}
     */
    elem["ShowSeatColor"] = function(canCreateDiscussions) {
        this["ShowColor"] = canCreateDiscussions;
        if (this["ShowColor"]) {
            this["SeatObject"]["setFill"](GetSeatColorByGuestType(this.GuestID));
        } else {
            this["SeatObject"]["setFill"]("white");
        }
    };
    /**
     * @param {?} statisticName
     * @return {?}
     */
    elem["SitGuest"] = function(statisticName) {
        if (this.IsFree() == false) {
            return false;
        }
        if (tablePlan["Guests"][statisticName] != null) {
            if (!tablePlan["Guests"][statisticName].IsUnseated()) {
                tablePlan["Tables"][tablePlan["Guests"][statisticName]["GuestTable"]][
                    "Seats"
                    ][tablePlan["Guests"][statisticName]["GuestSeat"]].UnSitGuest();
            }
            this["GuestID"] = statisticName;
            tablePlan["Guests"][this["GuestID"]]["GuestSeat"] = this["SeatID"];
            tablePlan["Guests"][this["GuestID"]]["GuestTable"] = this["TableID"];
            this.SetGuest(statisticName);
            return true;
        } else {
            return false;
        }
    };
    /**
     * @return {undefined}
     */
    elem["UnSitGuest"] = function() {
        /** @type {null} */
        tablePlan["Guests"][this["GuestID"]]["GuestSeat"] = null;
        /** @type {null} */
        tablePlan["Guests"][this["GuestID"]]["GuestTable"] = null;
        /** @type {null} */
        this["GuestID"] = null;
        this.SetGuest(null);
    };
    /**
     * @param {?} canCreateDiscussions
     * @return {undefined}
     */
    elem["TableWasRotated"] = function(canCreateDiscussions) {
        this["GuestNameObject"]["setRotationDeg"](-canCreateDiscussions);
    };
    elem["on"]("mousedown", function(response) {
        var event = response["evt"];
        if (event["which"] != 1) {
            return;
        }
        /** @type {boolean} */
        event["cancelBubble"] = true;
        if (this.IsFree() == true) {
            return;
        }
        if (tablePlan["DraggedGuestAvatar"] != null) {
            console["log"]("critical issue. Some guest is already dragged.");
            return;
        }
        event = fixEvent(event);
        var $plannerCanvas =$(".plannerCanvas");
        tablePlan["DraggedGuestAvatar"] = new GuestAvatar(
            this,
            this.GuestID,
            event["pageX"] - $plannerCanvas.offset().left,
            event["pageY"] - $plannerCanvas.offset().top
        );
        kineticLayer["add"](tablePlan.DraggedGuestAvatar);
        tablePlan["DraggedGuestAvatar"]["fire"]("mousedown");
        tablePlan["DraggedGuestAvatar"]["fire"]("dragstart");
    });
    elem["on"]("mouseover", function() {
        if (!this.IsFree()) {
            /** @type {string} */
            document["body"]["style"]["cursor"] = "pointer";
        }
    });
    elem["on"]("mouseout", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "default";
    });
    elem["on"]("click", function(results) {
        var available = results["evt"];
        if (available["which"] === 3) {
            if (this.IsFree()) {
                return;
            }
            var artistTrack = document["getElementById"]("seatPropertiesMenu");
            if (artistTrack == null) {
                return;
            }
            SetTopLeftOfPopupMenu(available, artistTrack);
            artistTrack["GuestID"] = this["GuestID"];
        }
    });
    return elem;
}
/**
 * @param {!Object} header
 * @return {?}
 */
function GetSeatColorByGuestType(header) {
    if (header == null) {
        return "white";
    }
    switch (parseInt(tablePlan["Guests"][header].GuestType)) {
        case 0:
            return "white";
        case 1:
        case 3:
            return "PowderBlue";
        case 2:
        case 4:
            return "Pink";
        case 5:
            return "Wheat";
        case 6:
            return "Orange";
        default:
            console["log"]("Guest type is unknown.");
            return "white";
    }
}
/**
 * @param {!Object} header
 * @return {?}
 */
function GetSeatSizeByGuestType(header) {
    if (header == null) {
        return _seatSize;
    }
    switch (parseInt(tablePlan["Guests"][header].GuestType)) {
        case 0:
        case 1:
        case 2:
        case 6:
            return _seatSize;
        case 3:
        case 4:
        case 5:
            return _seatChildSize;
        default:
            console["log"]("Guest type is unknown");
            return _seatSize;
    }
}
