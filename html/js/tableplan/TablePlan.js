"use strict";
// var sources = {
//     anti_clock_rotation: "/Images/planner/anti_clock_rotation.png",
//     clock_rotation: "/Images/planner/clock_rotation.png"
// };
/**
 * @param {?} dst
 * @param {?} val
 * @return {undefined}
 */
function TablePlan(dst, val) {
    this["images"] = {};
    /** @type {number} */
    var num_summed = 0;
    /** @type {number} */
    var summands = 0;
    var i;
    for (i in sources) {
        summands++;
    }
    for (i in sources) {
        /** @type {!Image} */
        this["images"][i] = new Image();
        this["images"][i]["src"] = sources[i];
        /**
         * @return {undefined}
         */
        this["images"][i]["onload"] = function() {
            if (++num_summed >= summands) {
                kineticLayer["draw"]();
            }
        };
    }
    this["PlanID"] = dst;
    this["Name"] = val;
    this["Tables"] = {};
    this["Guests"] = {};
    this["RectObjects"] = {};
    /** @type {boolean} */
    this["HideGrid"] = false;
    /** @type {string} */
    this["UserType"] = "";
    /** @type {null} */
    this["DraggedGuestAvatar"] = null;
    /** @type {!Array} */
    this["MenuList"] = [];
    /** @type {string} */
    this["MenuList"][0] = "-----";
    /** @type {string} */
    this["MenuList"][1] =
        "\u0421\u0442\u0430\u043d\u0434\u0430\u0440\u0442\u043d\u043e\u0435";
    /** @type {string} */
    this["MenuList"][2] = "\u0414\u0435\u0442\u0441\u043a\u043e\u0435";
    /** @type {string} */
    this["MenuList"][3] =
        "\u0412\u0435\u0433\u0430\u0442\u0430\u0440\u0438\u0430\u043d\u0441\u043a\u043e\u0435";
    /** @type {string} */
    this["MenuList"][4] = "\u0412\u0430\u0440\u0438\u0430\u043d\u0442 1";
    /** @type {string} */
    this["MenuList"][5] = "\u0412\u0430\u0440\u0438\u0430\u043d\u0442 2";
    /** @type {string} */
    this["MenuList"][6] = "\u0412\u0430\u0440\u0438\u0430\u043d\u0442 3";
    /** @type {string} */
    this["MenuList"][7] = "\u0412\u0430\u0440\u0438\u0430\u043d\u0442 4";
    /** @type {string} */
    this["MenuList"][8] = "\u0412\u0430\u0440\u0438\u0430\u043d\u0442 5";
    /** @type {string} */
    this["MenuList"][9] = "\u0412\u0430\u0440\u0438\u0430\u043d\u0442 6";
    /**
     * @return {?}
     */
    this["GetNumberOfTables"] = function() {
        /** @type {number} */
        var _0xc94ax8 = 0;
        var _0xc94ax9;
        for (_0xc94ax9 in this["Tables"]) {
            _0xc94ax8++;
        }
        return _0xc94ax8;
    };
    /**
     * @param {string} name
     * @param {string} definition
     * @param {number} serializer
     * @param {number} format
     * @param {number} log
     * @param {number} canCreateDiscussions
     * @param {number} mmCoreSecondsYear
     * @param {number} modstatus
     * @return {?}
     */
    this["AddNewTable"] = function(
        name,
        definition,
        serializer,
        format,
        log,
        canCreateDiscussions,
        mmCoreSecondsYear,
        modstatus
    ) {
        var table = new Table(
            name,
            definition,
            serializer,
            format,
            log,
            canCreateDiscussions,
            mmCoreSecondsYear,
            modstatus,
            this["images"]
        );
        this["Tables"][table["TableID"]] = table;
        kineticLayer["add"](table);
        return table;
    };
    /**
     * @param {number} error_type
     * @param {number} git_repo
     * @param {number} git_branch_tag_commit
     * @param {number} git_destination_path
     * @param {number} original_error
     * @param {number} stack_trace
     * @return {?}
     */
    this["AddNewRectObject"] = function(
        error_type,
        git_repo,
        git_branch_tag_commit,
        git_destination_path,
        original_error,
        stack_trace
    ) {
        var gitCallError = new RectObject(
            error_type,
            git_repo,
            git_branch_tag_commit,
            git_destination_path,
            original_error,
            stack_trace
        );
        this["RectObjects"][gitCallError["ObjectID"]] = gitCallError;
        kineticLayer["add"](gitCallError);
        return gitCallError;
    };
    /**
     * @param {?} ballNumber
     * @return {undefined}
     */
    this["AddSeatToTable"] = function(ballNumber) {
        var ball = this["Tables"][ballNumber];
        if (ball == null) {
            console["log"]("trying to add seat on unavailable table");
            return;
        }
        ball.AddSeat(null);
    };
    /**
     * @param {string} reverseControl
     * @param {string} context
     * @param {number} eventName
     * @param {number} bindOnce
     * @param {number} modstatus
     * @param {?} queryLayerControllerId
     * @param {?} name
     * @return {?}
     */
    this["AddNewGuest"] = function(
        reverseControl,
        context,
        eventName,
        bindOnce,
        modstatus,
        queryLayerControllerId,
        name
    ) {
        var internalCompute = new Guest(
            reverseControl,
            context,
            eventName,
            bindOnce,
            modstatus,
            null,
            null
        );
        this["Guests"][internalCompute["GuestID"]] = internalCompute;
        if (!IsNullOrEmpty(queryLayerControllerId) && !IsNullOrEmpty(name)) {
            var indexesByNodeName = this["Tables"][queryLayerControllerId];
            if (indexesByNodeName != null) {
                var foreignControls = indexesByNodeName["Seats"][name];
                if (foreignControls != null) {
                    foreignControls.SitGuest(reverseControl);
                }
            }
        }
        if (internalCompute.IsUnseated() == true) {
            unseatedGuestListControl.AddGuestToList(internalCompute);
        }
        return internalCompute;
    };
    /**
     * @param {?} logical_test
     * @param {?} query
     * @return {?}
     */
    this["GetSeatUnderClientXY"] = function(logical_test, query) {
        /** @type {null} */
        var validationVM = null;
        var indexLookupKey;
        for (indexLookupKey in this["Tables"]) {
            var v = tablePlan["Tables"][indexLookupKey].GetTableSeatUnderClientXY(
                logical_test,
                query
            );
            if (v != null) {
                validationVM = v;
            }
        }
        return validationVM;
    };
    /**
     * @param {!Object} PL$63
     * @return {undefined}
     */
    this["onMoveAnimation"] = function(PL$63) {
        var PL$67 = document["getElementById"]("plannerCanvas");
        if (this["DraggedGuestAvatar"] != null) {
            var _0xc94ax22 = tablePlan.GetSeatUnderClientXY(
                PL$63["pageX"] - parseInt(PL$67["style"]["left"]),
                PL$63["pageY"] - parseInt(PL$67["style"]["top"])
            );
            if (_0xc94ax22 != null) {
                if (dragAndDropSeatOver != null) {
                    if (dragAndDropSeatOver["SeatID"] != _0xc94ax22["SeatID"]) {
                        dragAndDropSeatOver.SelectSeat(false);
                        /** @type {null} */
                        dragAndDropSeatOver = null;
                    }
                }
                dragAndDropSeatOver = _0xc94ax22;
                _0xc94ax22.SelectSeat(true);
                kineticLayer["draw"]();
            } else {
                if (dragAndDropSeatOver != null) {
                    dragAndDropSeatOver.SelectSeat(false);
                    /** @type {null} */
                    dragAndDropSeatOver = null;
                    kineticLayer["draw"]();
                }
            }
        }
    };
    /**
     * @return {?}
     */
    this["GetData2Save"] = function() {
        /** @type {!Object} */
        var props = new Object();
        props["Id"] = this["PlanID"];
        props["Name"] = this["Name"];
        var varWikidataTypes = document["getElementById"]("planSizeY");
        props["Height"] = Math["round"](varWikidataTypes["value"] * 10);
        var priorityToColor = document["getElementById"]("planSizeX");
        props["Width"] = Math["round"](priorityToColor["value"] * 10);
        if (this["HideGrid"]) {
            /** @type {number} */
            props["HideGrid"] = 1;
        } else {
            /** @type {number} */
            props["HideGrid"] = 0;
        }
        /** @type {!Array} */
        props["Tables"] = [];
        /** @type {!Array} */
        props["Guests"] = [];
        /** @type {!Array} */
        props["MenuList"] = [];
        /** @type {!Array} */
        props["RectObjects"] = [];
        var indexLookupKey;
        for (indexLookupKey in this["Guests"]) {
            /** @type {!Object} */
            var data = new Object();
            data["Id"] = this["Guests"][indexLookupKey]["GuestID"];
            data["Name"] = this["Guests"][indexLookupKey]["GuestName"];
            /** @type {number} */
            data["Type"] = parseInt(this["Guests"][indexLookupKey].GuestType);
            /** @type {number} */
            data["RSVP"] = parseInt(this["Guests"][indexLookupKey].GuestRSVP);
            /** @type {number} */
            data["Meal"] = parseInt(this["Guests"][indexLookupKey].GuestMeal);
            data["TableID"] = this["Guests"][indexLookupKey]["GuestTable"];
            data["SeatID"] = this["Guests"][indexLookupKey]["GuestSeat"];
            props["Guests"]["push"](data);
        }
        var signedTransactionsCounter;
        for (signedTransactionsCounter in this["Tables"]) {
            /** @type {!Object} */
            var data = new Object();
            data["Id"] = this["Tables"][signedTransactionsCounter]["TableID"];
            data["Name"] = this["Tables"][signedTransactionsCounter][
                "TableNameObject"
                ]["getText"]();
            data["CenterX"] = Math["round"](
                this["Tables"][signedTransactionsCounter]["getX"]()
            );
            data["CenterY"] = Math["round"](
                this["Tables"][signedTransactionsCounter]["getY"]()
            );
            data["Angle"] = Math["round"](
                this["Tables"][signedTransactionsCounter]["getRotationDeg"]()
            );
            data["Type"] = this["Tables"][signedTransactionsCounter]["TableType"];
            /** @type {!Array} */
            data["Seats"] = [];
            var indexLookupKey;
            for (indexLookupKey in this["Tables"][signedTransactionsCounter][
                "Seats"
                ]) {
                /** @type {!Object} */
                var value = new Object();
                value["Id"] = this["Tables"][signedTransactionsCounter]["Seats"][
                    indexLookupKey
                    ]["SeatID"];
                data["Seats"]["push"](value);
            }
            props["Tables"]["push"](data);
        }
        var chartInstanceName;
        for (chartInstanceName in this["RectObjects"]) {
            /** @type {!Object} */
            var data = new Object();
            data["Id"] = this["RectObjects"][chartInstanceName]["ObjectID"];
            data["Name"] = this["RectObjects"][chartInstanceName]["ObjectNameObject"][
                "getText"
                ]();
            data["CenterX"] = Math["round"](
                this["RectObjects"][chartInstanceName]["getX"]()
            );
            data["CenterY"] = Math["round"](
                this["RectObjects"][chartInstanceName]["getY"]()
            );
            data["Width"] = Math["round"](
                this["RectObjects"][chartInstanceName]["ObjectRect"]["getWidth"]()
            );
            data["Height"] = Math["round"](
                this["RectObjects"][chartInstanceName]["ObjectRect"]["getHeight"]()
            );
            props["RectObjects"]["push"](data);
        }
        /** @type {number} */
        var PL$17 = 0;
        for (; PL$17 <= 9; ++PL$17) {
            props["MenuList"]["push"](this["MenuList"][PL$17]);
        }
        return props;
    };
    /**
     * @param {?} level
     * @return {?}
     */
    this["MenuId2String"] = function(level) {
        /** @type {number} */
        var input = parseInt(level);
        if (input < 1 || input > 9) {
            return "-----";
        } else {
            return this["MenuList"][input];
        }
    };
}
