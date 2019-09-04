﻿var dragManager = new function () {

    /**
     * составной объект для хранения информации о переносе:
     * {
     *   elem - элемент, на котором была зажата мышь
     *   avatar - аватар
     *   downX/downY - координаты, на которых был mousedown
     *   shiftX/shiftY - относительный сдвиг курсора от угла элемента
     * }
    */
    var dragObject = {};

    var self = this;

    function onMouseDown(e) {
        e = fixEvent(e);

        if (e.which != 1) return;

        var elem = findDraggable(e);
        if (!elem) return;

        dragObject.elem = elem;

        // запомним, что элемент нажат на текущих координатах pageX/pageY
        dragObject.downX = e.pageX;
        dragObject.downY = e.pageY;

        return false;
    }

    function onMouseMove(e) {
        if ((tablePlan !== undefined) && (tablePlan != null)) {
            if (tablePlan.DraggedGuestAvatar != null) {
                // guest is dragged from seat to seat. Canvas drag
                e = fixEvent(e);
                tablePlan.onMoveAnimation(e);
                return;
            }
        }

        if (!dragObject.elem)
            return; // элемент не зажат

        e = fixEvent(e);

        if (!dragObject.avatar) { // если перенос не начат...
            var moveX = e.pageX - dragObject.downX;
            var moveY = e.pageY - dragObject.downY;

            // если мышь передвинулась в нажатом состоянии недостаточно далеко
            if (Math.abs(moveX) < 3 && Math.abs(moveY) < 3) {
                return;
            }

            // начинаем перенос
            dragObject.avatar = createAvatar(e); // создать аватар
            if (!dragObject.avatar) { // отмена переноса, нельзя "захватить" за эту часть элемента
                dragObject = {};
                return;
            }

            // аватар создан успешно
            // создать вспомогательные свойства shiftX/shiftY
            var coords = getCoords(dragObject.avatar);
            dragObject.shiftX = dragObject.downX - coords.left;
            dragObject.shiftY = dragObject.downY - coords.top;

            startDrag(e); // отобразить начало переноса
        }

        // отобразить перенос объекта при каждом движении мыши
        self.onMoveAnimation(dragObject, e);

        dragObject.avatar.style.left = e.pageX - dragObject.shiftX + 'px';
        dragObject.avatar.style.top = e.pageY - dragObject.shiftY + 'px';

        return false;
    }

    function onMouseUp(e) {
        if (dragObject.avatar) { // если перенос идет
            e = fixEvent(e);
            finishDrag(e);
        }

        // перенос либо не начинался, либо завершился
        // в любом случае очистим "состояние переноса" dragObject
        dragObject = {};
    }

    function finishDrag(e) {
        // we do need to do check for resize boxes
        if ((dragObject.elem.className == "resize_horizontal") || (dragObject.elem.className == "resize_vertical")) {
            self.onDragEnd(dragObject, dropElem);
            dragObject = {};
            return;
        }

        var dropElem = findDroppable(e);

        if (!dropElem) {
            self.onDragCancel(dragObject);
        } else {
            var $planner = $(dropElem);
            // put on droppable object
            if ($planner.hasClass("plannerCanvas")) {
                // drop on table plan. Check for seats.
                var seat = tablePlan.GetSeatUnderClientXY(e.pageX - $planner.offset().left, e.pageY - $planner.offset().top);
                if (seat == null) {
                    self.onDragCancel(dragObject);
                } else {
                    // TODO: seat guest to the seat here
                    if (seat.SitGuest(dragObject.elem.id) == false) {
                        self.onDragCancel(dragObject);
                    }
                    else {
                        dragObject.elem.parentNode.removeChild(dragObject.elem);

                        // seat guest on the seat
                        self.onDragEnd(dragObject, dropElem);
                    }
                }
            } else {
                self.onDragEnd(dragObject, dropElem);
            }
        }
        dragObject = {};
    }

    function createAvatar(e) {

        // запомнить старые свойства, чтобы вернуться к ним при отмене переноса
        var avatar = dragObject.elem;
        var old = {
            parent: avatar.parentNode,
            nextSibling: avatar.nextSibling,
            position: avatar.style.position || '',
            left: avatar.style.left || '',
            top: avatar.style.top || '',
            zIndex: avatar.style.zIndex || ''
        };

        // функция для отмены переноса
        avatar.rollback = function () {
            old.parent.insertBefore(avatar, old.nextSibling);
            avatar.style.position = old.position;
            avatar.style.left = old.left;
            avatar.style.top = old.top;
            avatar.style.zIndex = old.zIndex
        };

        return avatar;
    }

    function startDrag(e) {
        var avatar = dragObject.avatar;
        // hide all open menus
        HideAllSubMenu();

        // инициировать начало переноса
        document.body.appendChild(avatar);
        avatar.style.zIndex = 9999;
        avatar.style.position = 'absolute';
    }

    function findDraggable(event) {
        var elem = event.target;
        while (elem != document && elem.getAttribute('draggable') == null) {
            elem = elem.parentNode;
        }
        return elem == document ? null : elem;
    }

    function findDroppable(event) {

        var elem = getElementUnderClientXY(dragObject.avatar, event.clientX, event.clientY);

        while (elem != document && elem.getAttribute('droppable') == null) {
            elem = elem.parentNode;
        }

        return elem == document ? null : elem;
    }

    document.onmousemove = onMouseMove;
    document.onmouseup = onMouseUp;
    document.onmousedown = onMouseDown;

    this.onDragEnd = function (dragObject, dropElem) {
        var $elem = $(dragObject.elem);

        // animation of End of drag and drop
        if ($elem.hasClass("resize_vertical")) {
            var $plannerField = $(".plannerCanvas"),
                $resize_vertical = $("#resize_vertical"),
                offset_top = $plannerField.offset().top;

            dragObject.avatar.style.left = dragObject.downX - dragObject.shiftX + 'px';
            $resize_vertical.css('top', $plannerField.height() + offset_top - 2);
        }
        if ($elem.hasClass("resize_horizontal")) {
            dragObject.avatar.style.top = dragObject.downY - dragObject.shiftY + 'px';
        }

        if ($elem.hasClass("pot_guest")) {
            if (dragAndDropSeatOver != null) {
                dragAndDropSeatOver.SelectSeat(false);
                dragAndDropSeatOver = null;
            }
        }

        kineticLayer.draw();
    };
    
    this.onDragCancel = function(dragObject) {
        // animation of cancel of drag and drop

        dragObject.avatar.rollback();
        VerticalResizePositionChanged();
        HorizontalResizePositionChanged();
    };
    
    this.onMoveAnimation = function (dragObject, fixedE) {
        // animation of moving of drag and drop
        var $plannerField = $(".plannerCanvas"),
            $side_menu = $("#side_menu"),
            planner_left = parseInt($plannerField.css('left')),
            offset_left = $plannerField.offset().left,
            offset_top = $plannerField.offset().top,
            $elem = $(dragObject.elem);

        switch (true) {
            case ($elem.hasClass("resize_horizontal")):
                if ((fixedE.pageX/* - offset_left*/) > _minimumPlanWidth + planner_left) {
                    dragObject.avatar.style.left = Math.floor(fixedE.pageX - dragObject.shiftX)/* - offset_left + planner_left*/ + 'px';
                    dragObject.avatar.style.top = dragObject.downY - dragObject.shiftY/* + offset_top*/ + 'px';

                    // change planner size
                    HorizontalResizePositionChanged();
                }
                break;
            case ($elem.hasClass("resize_vertical")):
                if (
                    ($side_menu.height() + offset_top) <= fixedE.pageY/* &&
                    fixedE.pageY > _minimumPlanHeight + parseInt($plannerField.css('top'))*/
                ) {
                    dragObject.avatar.style.left = dragObject.downX - dragObject.shiftX + 'px';
                    dragObject.avatar.style.top = Math.floor(fixedE.pageY - dragObject.shiftY) + 'px';

                    // change planner size
                    VerticalResizePositionChanged();
                }
                break;
            case ($elem.hasClass("pot_guest")):
                var _left = fixedE.pageX - dragObject.shiftX,
                    _top = fixedE.pageY - dragObject.shiftY;
                
                dragObject.avatar.style.left = _left + 'px';
                dragObject.avatar.style.top = _top + 'px';

                // drop on table plan. Check for seats.
                var seat = tablePlan.GetSeatUnderClientXY(_left - offset_left, _top - offset_top);
                if (seat != null) {
                    if (dragAndDropSeatOver != null)
                    {
                        if (dragAndDropSeatOver.SeatID != seat.SeatID) {
                            dragAndDropSeatOver.SelectSeat(false);
                            dragAndDropSeatOver = null;
                        }
                    }

                    dragAndDropSeatOver = seat;
                    seat.SelectSeat(true);
                    kineticLayer.draw();
                } else {
                    if (dragAndDropSeatOver != null) {
                        dragAndDropSeatOver.SelectSeat(false);
                        dragAndDropSeatOver = null;
                        kineticLayer.draw();
                    }
                }
                break;
            default:
                dragObject.avatar.style.left = fixedE.pageX - dragObject.shiftX + 'px';
                dragObject.avatar.style.top = fixedE.pageY - dragObject.shiftY + 'px';
                break;
        }
    }
};