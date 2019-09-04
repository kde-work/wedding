"use strict";
/**
 * @param {!Object} name
 * @param {number} v
 * @param {number} max
 * @param {?} format
 * @param {number} val
 * @param {number} offset
 * @param {?} table
 * @param {!Array} options
 * @param {!Object} model
 * @return {?}
 */
function Table(name, v, max, format, val, offset, table, options, model) {
    if (MinimumTableSeats(v) > max) {
        max = MinimumTableSeats(v);
    }
    var _this = new Kinetic.Group({
        draggable: true,
        x: val,
        y: offset
    });
    /**
     * @param {!Object} canCreateDiscussions
     * @return {undefined}
     */
    _this["ShowControls"] = function(canCreateDiscussions) {
        if (canCreateDiscussions == false) {
            if (this["TableType"] != 0) {
                this["ArrowLeftTop"]["hide"]();
                this["ArrowLeftBottom"]["hide"]();
                this["ArrowRightTop"]["hide"]();
                this["ArrowRightBottom"]["hide"]();
            }
            if (this["TableType"] == 7) {
                this["TableDesk"]["hide"]();
            }
            var indexLookupKey;
            for (indexLookupKey in this["Seats"]) {
                this["Seats"][indexLookupKey].ShowSeatColor(false);
            }
        } else {
            if (this["TableType"] != 0) {
                this["ArrowLeftTop"]["show"]();
                this["ArrowLeftBottom"]["show"]();
                this["ArrowRightTop"]["show"]();
                this["ArrowRightBottom"]["show"]();
            }
            if (this["TableType"] == 7) {
                this["TableDesk"]["show"]();
            }
            for (indexLookupKey in this["Seats"]) {
                this["Seats"][indexLookupKey].ShowSeatColor(true);
            }
        }
    };
    /**
     * @param {!Object} events
     * @return {undefined}
     */
    _this["ShowTableMenu"] = function(events) {
        var artistTrack = document["getElementById"]("tablePropertiesMenu");
        if (artistTrack == null) {
            return;
        }
        var event = events["evt"];
        if (event["which"] === 3) {
            /** @type {boolean} */
            event["cancelBubble"] = true;
            SetTopLeftOfPopupMenu(event, artistTrack);
            artistTrack["TableID"] = this["parent"]["TableID"];
            if (tablePlan["Tables"][this["parent"]["TableID"]]["TableType"] == 7) {
                /** @type {string} */
                $("#tablePropRename")[0]["style"]["display"] = "none";
            } else {
                /** @type {string} */
                $("#tablePropRename")[0]["style"]["display"] = "block";
            }
        }
    };
    /**
     * @param {number} length
     * @return {?}
     */
    _this["GetSeatX"] = function(length) {
        switch (this["TableType"]) {
            case 0:
                return (
                    (this["TableDesk"]["getRadius"]() + _seatSize / 2) *
                    Math["sin"]((length * 2 * Math["PI"]) / this["SeatsNumber"])
                );
                break;
            case 1:
                /** @type {number} */
                var leftoverLength = length % 4;
                /** @type {number} */
                length = parseInt(length / 4);
                if (leftoverLength == 0) {
                    return (
                        -this["TableDesk"]["getWidth"]() / 2 +
                        _seatSize / 2 +
                        _seatSize * length +
                        _seatsDelta * length
                    );
                } else {
                    if (leftoverLength == 1) {
                        return this["TableDesk"]["getWidth"]() / 2 + _seatSize / 2;
                    } else {
                        if (leftoverLength == 2) {
                            return (
                                this["TableDesk"]["getWidth"]() / 2 -
                                _seatSize / 2 -
                                _seatSize * length -
                                _seatsDelta * length
                            );
                        } else {
                            return -this["TableDesk"]["getWidth"]() / 2 - _seatSize / 2;
                        }
                    }
                }
                break;
            case 2:
                return (
                    -this["TableDesk"]["getWidth"]() / 2 +
                    _seatSize / 2 +
                    _seatSize * length +
                    _seatsDelta * length
                );
                break;
            case 3:
                return (
                    -this["TableDesk"]["getWidth"]() / 2 +
                    _seatSize / 2 +
                    _seatSize * parseInt(length / 2) +
                    _seatsDelta * parseInt(length / 2)
                );
                break;
            case 4:
                if (length == 0) {
                    return -this["TableDesk"]["getWidth"]() / 2 - _seatSize / 2;
                } else {
                    if (length == 1) {
                        return this["TableDesk"]["getWidth"]() / 2 + _seatSize / 2;
                    } else {
                        return (
                            -this["TableDesk"]["getWidth"]() / 2 +
                            _seatSize / 2 +
                            _seatSize * (length - 2) +
                            _seatsDelta * (length - 2)
                        );
                    }
                }
                break;
            case 5:
                if (length == 0) {
                    return -this["TableDesk"]["getWidth"]() / 2 - _seatSize / 2;
                } else {
                    /** @type {number} */
                    length = length - 1;
                    return (
                        -this["TableDesk"]["getWidth"]() / 2 +
                        _seatSize / 2 +
                        _seatSize * parseInt(length / 2) +
                        _seatsDelta * parseInt(length / 2)
                    );
                }
                break;
            case 6:
                if (length == 0) {
                    return -this["TableDesk"]["getWidth"]() / 2 - _seatSize / 2;
                } else {
                    if (length == 1) {
                        return this["TableDesk"]["getWidth"]() / 2 + _seatSize / 2;
                    } else {
                        /** @type {number} */
                        length = length - 2;
                        return (
                            -this["TableDesk"]["getWidth"]() / 2 +
                            _seatSize / 2 +
                            _seatSize * parseInt(length / 2) +
                            _seatsDelta * parseInt(length / 2)
                        );
                    }
                }
                break;
            case 7:
                return (
                    -this["TableDesk"]["getWidth"]() / 2 +
                    _seatSize +
                    _seatSize * length +
                    _seatsDelta * length
                );
                break;
            default:
                alert("incorrect table type in GetSeatX: " + this["TableType"]);
                return 0;
        }
        return 0;
    };
    /**
     * @param {number} length
     * @return {?}
     */
    _this["GetSeatY"] = function(length) {
        switch (this["TableType"]) {
            case 0:
                return (
                    (this["TableDesk"]["getRadius"]() + _seatSize / 2) *
                    Math["cos"]((length * 2 * Math["PI"]) / this["SeatsNumber"])
                );
                break;
            case 1:
                /** @type {number} */
                var leftoverLength = length % 4;
                /** @type {number} */
                length = parseInt(length / 4);
                if (leftoverLength == 0) {
                    return -this["TableDesk"]["getHeight"]() / 2 - _seatSize / 2;
                } else {
                    if (leftoverLength == 1) {
                        return (
                            -this["TableDesk"]["getHeight"]() / 2 +
                            _seatSize / 2 +
                            _seatSize * length +
                            _seatsDelta * length
                        );
                    } else {
                        if (leftoverLength == 2) {
                            return this["TableDesk"]["getHeight"]() / 2 + _seatSize / 2;
                        } else {
                            return (
                                this["TableDesk"]["getHeight"]() / 2 -
                                _seatSize / 2 -
                                _seatSize * length -
                                _seatsDelta * length
                            );
                        }
                    }
                }
                break;
            case 2:
                return -this["TableDesk"]["getHeight"]() / 2 - _seatSize / 2;
            case 3:
                if (length % 2 == 0) {
                    return -this["TableDesk"]["getHeight"]() / 2 - _seatSize / 2;
                } else {
                    return this["TableDesk"]["getHeight"]() / 2 + _seatSize / 2;
                }
                break;
            case 4:
                if (length == 0) {
                    return 0;
                } else {
                    if (length == 1) {
                        return 0;
                    } else {
                        return -this["TableDesk"]["getHeight"]() / 2 - _seatSize / 2;
                    }
                }
            case 5:
                if (length == 0) {
                    return 0;
                } else {
                    /** @type {number} */
                    length = length - 1;
                    if (length % 2 == 0) {
                        return -this["TableDesk"]["getHeight"]() / 2 - _seatSize / 2;
                    } else {
                        return this["TableDesk"]["getHeight"]() / 2 + _seatSize / 2;
                    }
                }
                break;
            case 6:
                if (length == 0) {
                    return 0;
                } else {
                    if (length == 1) {
                        return 0;
                    } else {
                        /** @type {number} */
                        length = length - 2;
                        if (length % 2 == 0) {
                            return -this["TableDesk"]["getHeight"]() / 2 - _seatSize / 2;
                        } else {
                            return this["TableDesk"]["getHeight"]() / 2 + _seatSize / 2;
                        }
                    }
                }
                break;
            case 7:
                return 0;
                break;
            default:
                alert("incorrect table type in GetSeatY: " + this["TableType"]);
                return 0;
        }
        return 0;
    };
    /**
     * @param {?} binds
     * @return {?}
     */
    _this["AddSeat"] = function(binds) {
        if (this["SeatsNumber"] >= 100) {
            DlgErrorFromServer(
                "\u0414\u043e\u0441\u0442\u0438\u0433\u043d\u0443\u0442 \u043c\u0430\u043a\u0441\u0438\u043c\u0443\u043c \u0447\u0438\u0441\u043b\u0430 \u0441\u0442\u0443\u043b\u044c\u0435\u0432 \u0437\u0430 \u043e\u0434\u043d\u0438\u043c \u0441\u0442\u043e\u043b\u043e\u0432: 100. \u0411\u043e\u043b\u044c\u0448\u0435 \u0441\u0442\u0443\u043b\u044c\u0435\u0432 \u0434\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043d\u0435\u043b\u044c\u0437\u044f, \u0434\u043e\u0431\u0430\u0432\u044c\u0442\u0435 \u0435\u0449\u0435 \u043e\u0434\u0438\u043d \u0441\u0442\u043e\u043b."
            );
            return false;
        }
        this["SeatsNumber"] += 1;
        /** @type {null} */
        var type = null;
        this.SetTableSize();
        type = new Seat(
            binds,
            null,
            this.GetSeatX(this["SeatsNumber"] - 1),
            this.GetSeatY(this["SeatsNumber"] - 1),
            this.TableID
        );
        this["Seats"][type["SeatID"]] = type;
        this["add"](type);
        kineticLayer["draw"]();
        return true;
    };
    /**
     * @param {?} k
     * @return {undefined}
     */
    _this["DeleteSeat"] = function(k) {
        if (this["SeatsNumber"] <= MinimumTableSeats(this.TableType)) {
            return;
        }
        this["Seats"][k]["destroy"]();
        delete this["Seats"][k];
        /** @type {number} */
        this["SeatsNumber"] = this["SeatsNumber"] - 1;
        this.SetTableSize();
        kineticLayer["draw"]();
    };
    /**
     * @return {undefined}
     */
    _this["SetTableSize"] = function() {
        switch (this["TableType"]) {
            case 0:
                this["TableDesk"]["setRadius"](
                    (1 * (1 + parseInt((this["SeatsNumber"] - 1) / 4)) * _seatSize) / 2
                );
                break;
            case 1:
                /** @type {number} */
                var _0x2ae8x14 = parseInt((this["SeatsNumber"] - 1) / 4) + 1;
                this["TableDesk"]["setWidth"](
                    _0x2ae8x14 * _seatSize + (_0x2ae8x14 - 1) * _seatsDelta
                );
                this["TableDesk"]["setHeight"](
                    _0x2ae8x14 * _seatSize + (_0x2ae8x14 - 1) * _seatsDelta
                );
                break;
            case 2:
                if (this["SeatsNumber"] <= 1) {
                    this["TableDesk"]["setWidth"](_seatSize);
                } else {
                    this["TableDesk"]["setWidth"](
                        this["SeatsNumber"] * _seatSize +
                        (this["SeatsNumber"] - 1) * _seatsDelta
                    );
                }
                break;
            case 3:
                if (this["SeatsNumber"] <= 2) {
                    this["TableDesk"]["setWidth"](_seatSize);
                } else {
                    this["TableDesk"]["setWidth"](
                        parseInt((this["SeatsNumber"] + 1) / 2) * _seatSize +
                        parseInt((this["SeatsNumber"] - 1) / 2) * _seatsDelta
                    );
                }
                break;
            case 4:
                if (this["SeatsNumber"] <= 3) {
                    this["TableDesk"]["setWidth"](_seatSize);
                } else {
                    this["TableDesk"]["setWidth"](
                        (this["SeatsNumber"] - 2) * _seatSize +
                        (this["SeatsNumber"] - 3) * _seatsDelta
                    );
                }
                break;
            case 5:
                if (this["SeatsNumber"] <= 3) {
                    this["TableDesk"]["setWidth"](_seatSize);
                } else {
                    this["TableDesk"]["setWidth"](
                        parseInt(this["SeatsNumber"] / 2) * _seatSize +
                        parseInt((this["SeatsNumber"] - 2) / 2) * _seatsDelta
                    );
                }
                break;
            case 6:
                if (this["SeatsNumber"] <= 4) {
                    this["TableDesk"]["setWidth"](_seatSize);
                } else {
                    this["TableDesk"]["setWidth"](
                        parseInt((this["SeatsNumber"] - 1) / 2) * _seatSize +
                        parseInt((this["SeatsNumber"] - 3) / 2) * _seatsDelta
                    );
                }
                break;
            case 7:
                this["TableDesk"]["setWidth"](
                    (this["SeatsNumber"] + 1) * _seatSize +
                    (this["SeatsNumber"] - 1) * _seatsDelta
                );
                break;
            default:
                alert("incorrect table type in SetTableSize: " + this["TableType"]);
                return;
        }
        switch (this["TableType"]) {
            case 0:
                break;
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
                this["TableDesk"]["setX"](-this["TableDesk"]["getWidth"]() / 2);
                this["TableDesk"]["setY"](-this["TableDesk"]["getHeight"]() / 2);
                this["ArrowLeftTop"]["setX"](-this["TableDesk"]["getWidth"]() / 2 + 1);
                this["ArrowLeftBottom"]["setX"](
                    -this["TableDesk"]["getWidth"]() / 2 + 13
                );
                this["ArrowRightTop"]["setX"](this["TableDesk"]["getWidth"]() / 2 - 13);
                this["ArrowRightBottom"]["setX"](
                    this["TableDesk"]["getWidth"]() / 2 - 1
                );
                this["ArrowLeftTop"]["setY"](-this["TableDesk"]["getHeight"]() / 2 + 1);
                this["ArrowLeftBottom"]["setY"](
                    this["TableDesk"]["getHeight"]() / 2 - 1
                );
                this["ArrowRightTop"]["setY"](
                    -this["TableDesk"]["getHeight"]() / 2 + 1
                );
                this["ArrowRightBottom"]["setY"](
                    this["TableDesk"]["getHeight"]() / 2 - 1
                );
                break;
            default:
                alert("incorrect table type in SetTableSize: " + this["TableType"]);
                return;
        }
        /** @type {number} */
        var artistTrack = 0;
        var indexLookupKey;
        for (indexLookupKey in this["Seats"]) {
            this["Seats"][indexLookupKey]["setX"](this.GetSeatX(artistTrack));
            this["Seats"][indexLookupKey]["setY"](this.GetSeatY(artistTrack));
            ++artistTrack;
        }
    };
    /**
     * @param {number} _aY
     * @param {?} _aX
     * @return {?}
     */
    _this["GetTableSeatUnderClientXY"] = function(_aY, _aX) {
        /** @type {null} */
        var subwiki = null;
        var i;
        for (i in this["Seats"]) {
            if (!this["Seats"][i].IsFree()) {
                continue;
            }
            var _0x2ae8x18 = this["Seats"][i]["getX"]();
            var _0x2ae8x19 = this["Seats"][i]["getY"]();
            /** @type {number} */
            var _0x2ae8x1a = _seatSize / 2;
            /** @type {number} */
            var _bY =
                this["getX"]() +
                _0x2ae8x18 * Math["cos"](this["getRotation"]()) -
                _0x2ae8x19 * Math["sin"](this["getRotation"]());
            var _bX =
                this["getY"]() +
                _0x2ae8x18 * Math["sin"](this["getRotation"]()) +
                _0x2ae8x19 * Math["cos"](this["getRotation"]());
            if (
                (_bY - _aY) * (_bY - _aY) + (_bX - _aX) * (_bX - _aX) <=
                _0x2ae8x1a * _0x2ae8x1a
            ) {
                subwiki = this["Seats"][i];
                break;
            }
        }
        return subwiki;
    };
    /**
     * @param {?} newText
     * @return {undefined}
     */
    _this["SetName"] = function(newText) {
        if (this["TableType"] == 7) {
            return;
        }
        this["TableNameObject"]["setText"](newText);
        this["TableNameObject"]["setOffset"]({
            x: _this["TableNameObject"]["getWidth"]() / 2,
            y: _this["TableNameObject"]["getHeight"]() / 2
        });
    };
    if (name == null) {
        _this["TableID"] = generateGuid();
    } else {
        /** @type {!Object} */
        _this["TableID"] = name;
    }
    /** @type {number} */
    _this["TableType"] = v;
    /** @type {number} */
    _this["SeatsNumber"] = 0;
    _this["Seats"] = {};
    /** @type {null} */
    _this["TableNameObject"] = null;
    /** @type {null} */
    _this["TableDesk"] = null;
    /** @type {null} */
    _this["ArrowLeftTop"] = null;
    /** @type {null} */
    _this["ArrofLeftBottpm"] = null;
    /** @type {null} */
    _this["ArrowRightTop"] = null;
    /** @type {null} */
    _this["ArrofRightBottom"] = null;
    /**
     * @param {?} canCreateDiscussions
     * @return {undefined}
     */
    _this["mouseClickLeft"] = function(canCreateDiscussions) {
        this["parent"].RotateTable(-45);
    };
    /**
     * @param {?} canCreateDiscussions
     * @return {undefined}
     */
    _this["mouseClickRight"] = function(canCreateDiscussions) {
        this["parent"].RotateTable(45);
    };
    /**
     * @param {number} stageWidth
     * @return {undefined}
     */
    _this["RotateTable"] = function(stageWidth) {
        var stageX = this["getRotationDeg"]();
        this["setRotationDeg"](stageX + stageWidth);
        this["TableNameObject"]["setRotationDeg"](-stageX - stageWidth);
        var indexLookupKey;
        for (indexLookupKey in this["Seats"]) {
            this["Seats"][indexLookupKey].TableWasRotated(stageX + stageWidth);
        }
        kineticLayer["draw"]();
    };
    switch (v) {
        case 0:
            _this["TableDesk"] = new Kinetic.Circle({
                x: 0,
                y: 0,
                radius: (1.2 * _seatSize) / 2,
                fill: "white",
                stroke: "gray",
                strokeWidth: 2
            });
            _this["TableDesk"]["on"]("click", _this.ShowTableMenu);
            _this["add"](_this.TableDesk);
            break;
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
        case 6:
            _this["TableDesk"] = new Kinetic.Rect({
                x: -_seatSize / 2,
                y: -_longTableHeight / 2,
                width: _seatSize,
                height: _longTableHeight,
                fill: "white",
                stroke: "gray",
                strokeWidth: 2
            });
            _this["TableDesk"]["on"]("click", _this.ShowTableMenu);
            _this["add"](_this.TableDesk);
            break;
        case 7:
            _this["TableDesk"] = new Kinetic.Rect({
                x: -_seatSize,
                y: -_seatSize,
                width: 2 * _seatSize,
                height: 2 * _seatSize,
                stroke: "lightgray",
                strokeWidth: 1
            });
            _this["TableDesk"]["on"]("click", _this.ShowTableMenu);
            _this["add"](_this.TableDesk);
            break;
    }
    if (1 <= v && v <= 7) {
        _this["ArrowLeftTop"] = new Kinetic.Image({
            x: -_this["TableDesk"]["getWidth"]() / 2 + 1,
            y: -_this["TableDesk"]["getHeight"]() / 2 + 1,
            image: model["anti_clock_rotation"],
            width: 12,
            height: 12
        });
        _this["ArrowLeftTop"]["on"]("mousedown", _this["mouseClickLeft"]);
        _this["add"](_this.ArrowLeftTop);
        _this["ArrowRightBottom"] = new Kinetic.Image({
            x: _this["TableDesk"]["getWidth"]() / 2 - 1,
            y: _this["TableDesk"]["getHeight"]() / 2 - 1,
            image: model["anti_clock_rotation"],
            rotationDeg: 180,
            width: 12,
            height: 12
        });
        _this["ArrowRightBottom"]["on"]("mousedown", _this["mouseClickLeft"]);
        _this["add"](_this.ArrowRightBottom);
        _this["ArrowRightTop"] = new Kinetic.Image({
            x: _this["TableDesk"]["getWidth"]() / 2 - 13,
            y: -_this["TableDesk"]["getHeight"]() / 2 + 1,
            image: model["clock_rotation"],
            width: 12,
            height: 12
        });
        _this["ArrowRightTop"]["on"]("mousedown", _this["mouseClickRight"]);
        _this["add"](_this.ArrowRightTop);
        _this["ArrowLeftBottom"] = new Kinetic.Image({
            x: -_this["TableDesk"]["getWidth"]() / 2 + 13,
            y: _this["TableDesk"]["getHeight"]() / 2 - 1,
            image: model["clock_rotation"],
            rotationDeg: 180,
            width: 12,
            height: 12
        });
        _this["ArrowLeftBottom"]["on"]("mousedown", _this["mouseClickRight"]);
        _this["add"](_this.ArrowLeftBottom);
    }
    _this["TableNameObject"] = new Kinetic.Text({
        x: 0,
        y: 0,
        text: "",
        fontSize: 15,
        fontFamily: "Calibri",
        fill: "black"
    });
    _this.SetName(format);
    _this["TableNameObject"]["on"]("click", _this.ShowTableMenu);
    _this["add"](_this.TableNameObject);
    _this["TableDesk"]["on"]("mouseover", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "pointer";
    });
    _this["TableDesk"]["on"]("mouseout", function() {
        /** @type {string} */
        document["body"]["style"]["cursor"] = "default";
    });
    /** @type {number} */
    var i = 0;
    for (; i < max; ++i) {
        /** @type {boolean} */
        var _0x2ae8x21 = false;
        if (options != null) {
            _0x2ae8x21 = _this.AddSeat(options[i]);
        } else {
            _0x2ae8x21 = _this.AddSeat(null);
        }
        if (_0x2ae8x21 == false) {
            break;
        }
    }
    _this.RotateTable(table);
    return _this;
}
/**
 * @param {number} leafletId
 * @return {?}
 */
function MinimumTableSeats(leafletId) {
    switch (leafletId) {
        case 0:
        case 1:
        case 2:
        case 3:
            return 1;
        case 4:
        case 5:
            return 3;
        case 6:
            return 4;
        case 7:
            return 1;
        default:
            return 1;
    }
}
