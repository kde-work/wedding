"use strict";
/**
 * @param {!Object} uuid
 * @param {?} query
 * @param {number} method
 * @param {number} matchAny
 * @param {number} origin_width
 * @param {number} origin_height
 * @return {?}
 */
function RectObject(
    uuid,
    query,
    method,
    matchAny,
    origin_width,
    origin_height
) {
    var doc = new Kinetic.Group({
        draggable: true,
        x: method,
        y: matchAny
    });
    /**
     * @param {!Object} canCreateDiscussions
     * @return {undefined}
     */
    doc["ShowControls"] = function(canCreateDiscussions) {
        if (canCreateDiscussions == false) {
            this["ResizeHorizontal"]["hide"]();
            this["ResizeVertical"]["hide"]();
            this["ObjectRect"]["setFill"]("white");
        } else {
            this["ResizeHorizontal"]["show"]();
            this["ResizeVertical"]["show"]();
            this["ObjectRect"]["setFill"]("lightgray");
        }
    };
    /**
     * @param {!Object} events
     * @return {undefined}
     */
    doc["ShowObjectMenu"] = function(events) {
        var artistTrack = document["getElementById"]("objectPropertiesMenu");
        if (artistTrack == null) {
            return;
        }
        var event = events["evt"];
        if (event["which"] === 3) {
            /** @type {boolean} */
            event["cancelBubble"] = true;
            SetTopLeftOfPopupMenu(event, artistTrack);
            artistTrack["ObjectID"] = this["parent"]["ObjectID"];
        }
    };
    /**
     * @param {?} newText
     * @return {undefined}
     */
    doc["SetName"] = function(newText) {
        this["ObjectNameObject"]["setText"](newText);
        this["ObjectNameObject"]["setOffset"]({
            x: doc["ObjectNameObject"]["getWidth"]() / 2,
            y: doc["ObjectNameObject"]["getHeight"]() / 2
        });
    };
    /**
     * @return {undefined}
     */
    doc["SetResizeBoxPossitions"] = function() {
        this["ResizeHorizontal"]["setX"](this["ObjectRect"]["getWidth"]() / 2 - 3);
        this["ResizeHorizontal"]["setY"](0);
        doc["ResizeVertical"]["setX"](0);
        doc["ResizeVertical"]["setY"](this["ObjectRect"]["getHeight"]() / 2 - 3);
    };
    if (uuid == null) {
        doc["ObjectID"] = generateGuid();
    } else {
        /** @type {!Object} */
        doc["ObjectID"] = uuid;
    }
    /** @type {null} */
    doc["ObjectNameObject"] = null;
    /** @type {null} */
    doc["ObjectRect"] = null;
    doc["ObjectRect"] = new Kinetic.Rect({
        x: -origin_width / 2,
        y: -origin_height / 2,
        width: origin_width,
        height: origin_height,
        fill: "lightgray",
        stroke: "gray",
        strokeWidth: 2
    });
    doc["ObjectRect"]["on"]("click", doc.ShowObjectMenu);
    doc["add"](doc.ObjectRect);
    doc["ObjectNameObject"] = new Kinetic.Text({
        x: 0,
        y: 0,
        text: "",
        fontSize: 15,
        fontFamily: "Calibri",
        fill: "black"
    });
    doc.SetName(query);
    doc["ObjectNameObject"]["on"]("click", doc.ShowObjectMenu);
    doc["add"](doc.ObjectNameObject);
    doc["ObjectRect"]["on"]("mouseover", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "pointer";
    });
    doc["ObjectRect"]["on"]("mouseout", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "default";
    });
    doc["ResizeHorizontal"] = new Kinetic.Rect({
        draggable: true,
        width: 7,
        height: 7,
        fill: "white",
        stroke: "black",
        strokeWidth: 1
    });
    doc["ResizeHorizontal"]["Parent"] = doc;
    doc["ResizeVertical"] = new Kinetic.Rect({
        draggable: true,
        width: 7,
        height: 7,
        fill: "white",
        stroke: "black",
        strokeWidth: 1
    });
    doc["ResizeVertical"]["Parent"] = doc;
    doc.SetResizeBoxPossitions();
    doc["ResizeVertical"]["on"]("mouseover", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "n-resize";
    });
    doc["ResizeVertical"]["on"]("mouseout", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "default";
    });
    doc["ResizeHorizontal"]["on"]("mouseover", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "e-resize";
    });
    doc["ResizeHorizontal"]["on"]("mouseout", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "default";
    });
    doc["ResizeVertical"]["on"]("dragmove", function() {
        var _0xb15axe = this["getX"]();
        var next_planting = this["getY"]();
        if (_0xb15axe != 0) {
            this["setX"](0);
        }
        if (
            next_planting + this["Parent"]["ObjectRect"]["getHeight"]() / 2 <
            _seatSize / 2
        ) {
            /** @type {number} */
            next_planting =
                -this["Parent"]["ObjectRect"]["getHeight"]() / 2 + _seatSize / 2;
            this["setY"](next_planting);
        }
        var Ytitle = this["Parent"]["ObjectRect"]["getY"]();
        var groupsize = this["Parent"]["ObjectRect"]["getHeight"]();
        var next_grow =
            next_planting + this["Parent"]["ObjectRect"]["getHeight"]() / 2;
        /** @type {number} */
        var dragstocreate = groupsize - next_grow;
        this["Parent"]["setY"](this["Parent"]["getY"]() - dragstocreate / 2);
        this["Parent"]["ObjectRect"]["setY"](Ytitle + dragstocreate / 2);
        this["Parent"]["ObjectRect"]["setHeight"](groupsize - dragstocreate);
        this["Parent"].SetResizeBoxPossitions();
    });
    doc["ResizeHorizontal"]["on"]("dragmove", function() {
        var next_planting = this["getX"]();
        var _0xb15axf = this["getY"]();
        if (_0xb15axf != 0) {
            this["setY"](0);
        }
        if (
            next_planting + this["Parent"]["ObjectRect"]["getWidth"]() / 2 <
            _seatSize / 2
        ) {
            /** @type {number} */
            next_planting =
                -this["Parent"]["ObjectRect"]["getWidth"]() / 2 + _seatSize / 2;
            this["setX"](next_planting);
        }
        var Ytitle = this["Parent"]["ObjectRect"]["getX"]();
        var groupsize = this["Parent"]["ObjectRect"]["getWidth"]();
        var next_grow =
            next_planting + this["Parent"]["ObjectRect"]["getWidth"]() / 2;
        /** @type {number} */
        var dragstocreate = groupsize - next_grow;
        this["Parent"]["setX"](this["Parent"]["getX"]() - dragstocreate / 2);
        this["Parent"]["ObjectRect"]["setX"](Ytitle + dragstocreate / 2);
        this["Parent"]["ObjectRect"]["setWidth"](groupsize - dragstocreate);
        this["Parent"].SetResizeBoxPossitions();
    });
    doc["add"](doc.ResizeHorizontal);
    doc["add"](doc.ResizeVertical);
    return doc;
}
