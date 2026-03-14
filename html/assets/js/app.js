let instance = undefined;

/** Custom labels for connections (connection.id -> string). Empty string = use default "source → target". */
let connectionLabels = {};

function getConnectionNodeIds(connection) {
    var srcId = (connection.source && connection.source.id) ? connection.source.id : '';
    var tgtId = (connection.target && connection.target.id) ? connection.target.id : '';
    var srcNum = srcId.replace(/^flowchartWindow/, '');
    var tgtNum = tgtId.replace(/^flowchartWindow/, '');
    return { srcNum: srcNum, tgtNum: tgtNum };
}

function getConnectionDefaultLabel(connection) {
    var ids = getConnectionNodeIds(connection);
    if (ids.srcNum && ids.tgtNum) {
        return ids.srcNum + ' \u2192 ' + ids.tgtNum;
    }
    return (connection.sourceId || '') + '-' + (connection.targetId || '');
}

function getConnectionDisplayLabel(connection) {
    var custom = connectionLabels[connection.id];
    if (custom !== undefined && custom !== '') {
        return custom;
    }
    return getConnectionDefaultLabel(connection);
}

function setConnectionLabel(connection, label) {
    var overlay = connection.getOverlay('label');
    if (overlay) {
        overlay.setLabel(label || getConnectionDefaultLabel(connection));
    }
}

jsPlumb.ready(function () {
    instance = window.jsp = window.instance = jsPlumb.getInstance({
        DragOptions: { cursor: 'pointer', zIndex: 2000 },
        ConnectionOverlays: [
            ['Arrow', {
                location: 1,
                visible: true,
                width: 11,
                length: 11,
                id: 'ARROW'
            }],
            ['Label', {
                location: 0.5,
                id: 'label',
                cssClass: 'aLabel'
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
            setConnectionLabel(connection, getConnectionDisplayLabel(connection));
        };

    instance._addEndpoints = function (toId, sourceAnchors, targetAnchors) {
        let currentEndpoint = undefined;
        let elementId = 'flowchart' + toId;
        let el = instance.getElement(elementId);
        if (!el) {
            return;
        }

        for (let i = 0; i < sourceAnchors.length; i++) {
            const sourceUUID = toId + sourceAnchors[i];
            currentEndpoint = instance.addEndpoint(el, sourceEndpoint, { anchor: sourceAnchors[i], uuid: sourceUUID });
        }

        for (let j = 0; j < targetAnchors.length; j++) {
            const targetUUID = toId + targetAnchors[j];
            currentEndpoint = instance.addEndpoint(el, targetEndpoint, { anchor: targetAnchors[j], uuid: targetUUID });
        }

        if (typeof instance.revalidate === 'function') {
            instance.revalidate(el);
        }
    };

    instance.batch(function () {
        //instance._addEndpoints('Window1', ['RightMiddle'], []);
        //instance._addEndpoints('Window2', ['RightMiddle'], ['LeftMiddle']);
        //instance._addEndpoints('Window3', [], ['LeftMiddle']);

        instance.bind('connection', function (connInfo, originalEvent) {
            init(connInfo.connection);
        });

        instance.draggable(jsPlumb.getSelector('.flowchart-demo .window'), { grid: [20, 20] });

        //instance.connect({ uuids: ['Window1RightMiddle', 'Window2LeftMiddle'], detachable: true, editable: true });
        //instance.connect({ uuids: ['Window2RightMiddle', 'Window3LeftMiddle'], detachable: true, editable: true });

        instance.bind('click', function (connection, originalEvent) {
            if (!connection || !connection.getOverlay('label')) return;
            var current = connectionLabels[connection.id];
            if (current === undefined) current = '';
            var defaultLabel = getConnectionDefaultLabel(connection);
            var newLabel = window.prompt('Connection label (optional). Leave empty for default "' + defaultLabel + '".', current);
            if (newLabel === null) return;
            connectionLabels[connection.id] = newLabel.trim();
            setConnectionLabel(connection, connectionLabels[connection.id] || null);
        });

        instance.bind('connectionDrag', function (connection) {
            console.log('connection ' + connection.id + ' is being dragged');
        });

        instance.bind('connectionDragStop', function ( connection ) {
            let endpoints = connection.endpoints;

            let originEndpoint = endpoints[0];
            let targetEndpoint = endpoints[1];

            let originStepElementId = originEndpoint.elementId;
            let targetStepElementId = targetEndpoint.elementId;

            console.log( 'originStepElementId = ' + originStepElementId );
            console.log( 'targetStepElementId = ' + targetStepElementId );

            // Do validations of origin step and target step. If connection not valid, delete it.

            //instance.deleteConnection( connection );
        });

        instance.bind('connectionMoved', function (params) {
            console.log('connection ' + params.connection.id + ' was moved');
        });
    });
});
