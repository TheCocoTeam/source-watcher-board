let instance = undefined;

jsPlumb.ready(function () {
    instance = window.jsp = jsPlumb.getInstance({
        DragOptions: { cursor: 'pointer', zIndex: 2000 },
        ConnectionOverlays: [
            ['Arrow', {
                location: 1,
                visible: true,
                width: 11,
                length: 11,
                id: 'ARROW',
                events: {
                    click: function () {
                        alert('you clicked on the arrow overlay');
                    }
                }
            }],
            ['Label', {
                location: 0.1,
                id: 'label',
                cssClass: 'aLabel',
                events: {
                    tap: function () {
                        alert('hey');
                    }
                }
            }]
        ],
        Container: 'canvas'
    });

    // https://docs.jsplumbtoolkit.com/toolkit/current/articles/connectors.html

    const basicType = {
        //connector: 'Bezier',
        connector: 'Flowchart',
        //connector: 'StateMachine',
        //connector: 'Straight',
        paintStyle: { stroke: 'red', strokeWidth: 4 },
        hoverPaintStyle: { stroke: 'blue' },
        overlays: [
            'Arrow'
        ]
    };
    instance.registerConnectionType('basic', basicType);

    const connectorPaintStyle = {
            strokeWidth: 2,
            stroke: '#61B7CF',
            joinstyle: 'round',
            outlineStroke: 'white',
            outlineWidth: 2
        },
        connectorHoverStyle = {
            strokeWidth: 3,
            stroke: '#216477',
            outlineWidth: 5,
            outlineStroke: 'white'
        },
        endpointHoverStyle = {
            fill: '#216477',
            stroke: '#216477'
        },
        sourceEndpoint = {
            endpoint: 'Dot',
            paintStyle: {
                stroke: '#7AB02C',
                fill: 'transparent',
                radius: 7,
                strokeWidth: 1
            },
            isSource: true,
            connector: ['Flowchart', { stub: [40, 60], gap: 10, cornerRadius: 5, alwaysRespectStubs: true }],
            connectorStyle: connectorPaintStyle,
            hoverPaintStyle: endpointHoverStyle,
            connectorHoverStyle: connectorHoverStyle,
            dragOptions: {},
            overlays: [
                ['Label', {
                    location: [0.5, 1.5],
                    label: 'Drag',
                    cssClass: 'endpointSourceLabel',
                    visible: false
                }]
            ]
        },
        targetEndpoint = {
            endpoint: 'Dot',
            paintStyle: { fill: '#7AB02C', radius: 7 },
            hoverPaintStyle: endpointHoverStyle,
            maxConnections: -1,
            dropOptions: { hoverClass: 'hover', activeClass: 'active' },
            isTarget: true,
            overlays: [
                ['Label', { location: [0.5, -0.5], label: 'Drop', cssClass: 'endpointTargetLabel', visible: false }]
            ]
        },
        init = function (connection) {
            console.dir(connection);
            connection.getOverlay('label').setLabel(connection.sourceId.substring(15) + '-' + connection.targetId.substring(15));
        };

    instance._addEndpoints = function (toId, sourceAnchors, targetAnchors) {
        let currentEndpoint = undefined;
        let element = 'flowchart' + toId;

        for (let i = 0; i < sourceAnchors.length; i++) {
            const sourceUUID = toId + sourceAnchors[i];
            console.dir(sourceUUID);
            currentEndpoint = instance.addEndpoint(element, sourceEndpoint, { anchor: sourceAnchors[i], uuid: sourceUUID });
        }

        for (let j = 0; j < targetAnchors.length; j++) {
            const targetUUID = toId + targetAnchors[j];
            console.dir(targetUUID);
            currentEndpoint = instance.addEndpoint(element, targetEndpoint, { anchor: targetAnchors[j], uuid: targetUUID });
        }
    };

    instance.batch(function () {
        instance._addEndpoints('Window1', ['RightMiddle'], []);
        instance._addEndpoints('Window2', ['RightMiddle'], ['LeftMiddle']);
        instance._addEndpoints('Window3', [], ['LeftMiddle']);

        instance.bind('connection', function (connInfo, originalEvent) {
            init(connInfo.connection);
        });

        instance.draggable(jsPlumb.getSelector('.flowchart-demo .window'), { grid: [20, 20] });

        instance.connect({ uuids: ['Window1RightMiddle', 'Window2LeftMiddle'], detachable: true, editable: true });
        instance.connect({ uuids: ['Window2RightMiddle', 'Window3LeftMiddle'], detachable: true, editable: true });

        instance.bind('click', function (connection, originalEvent) {
            connection.toggleType('basic');
        });

        instance.bind('connectionDrag', function (connection) {
            console.log('connection ' + connection.id + ' is being dragged. suspendedElement is ', connection.suspendedElement, ' of type ', connection.suspendedElementType);
        });

        instance.bind('connectionDragStop', function (connection) {
            console.log('connection ' + connection.id + ' was dragged');
        });

        instance.bind('connectionMoved', function (params) {
            console.log('connection ' + params.connection.id + ' was moved');
        });
    });
});
