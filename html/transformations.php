<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <title>Transformations</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.min.css">

    <script src="assets/js/jsplumb.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
    <script src="assets/js/app.js"></script>

    <link rel="stylesheet" href="assets/css/jsplumbtoolkit-defaults.css">
    <link rel="stylesheet" href="assets/css/jsplumbtoolkit-demo.css">
    <link rel="stylesheet" href="assets/css/app.css">

    <script src="assets/js/generic/tools.js"></script>
    <script src="assets/js/views/transformations.js"></script>
</head>
<body>
<div id="main-container">
    <div id="rows-container">
        <div id="top-menu-container">
            <span class="top-menu-title">Source Watcher</span>
            <button type="button" id="save-transformation-btn" class="top-menu-button">Save transformation</button>
            <a href="login.php" id="logout-btn">Log out</a>
        </div>

        <div id="central-container">
            <aside class="sidebar-panel">
                <h2 class="sidebar-title">Steps</h2>
                <p class="sidebar-hint">Drag a step onto the canvas</p>
                <div id="left-container"></div>
            </aside>

            <div id="diagram-container" ondragover="allowDrop(event)" ondrop="drop(event)">
                <div class="canvas-wide flowchart-demo jtk-surface jtk-surface-nopan" id="canvas">
                    <!--
                    <div class="window jtk-node" id="flowchartWindow1"><center><strong>Csv</strong><br/><label>Extractor</label></center></div>
                    <div class="window jtk-node" id="flowchartWindow2"><center><strong>Rename Columns</strong><br/><label>Transformer</label></center></div>
                    <div class="window jtk-node" id="flowchartWindow3"><center><strong>Database</strong><br/><label>Loader</label></center></div>
                    -->
                </div>
            </div>
        </div>

        <div id="bottom-container">
            Ready
        </div>
    </div>
</div>

<!-- Step edit modal (for CSV and other step types) -->
<div id="step-edit-modal" title="Edit step" style="display: none;">
    <form id="step-edit-form">
        <input type="hidden" id="edit-numeric-id" name="numericId" value="">
        <div id="step-description-text" class="edit-hint" style="display:none; margin-bottom:8px;"></div>
        <div id="edit-csv-fields" class="edit-step-fields">
            <div class="form-group">
                <label for="csv-file-path">File path <span class="required">*</span></label>
                <input type="text" id="csv-file-path" name="filePath" placeholder="/path/to/file.csv or https://...">
            </div>
            <div class="form-group">
                <label for="csv-columns">Columns (comma-separated, leave empty for all)</label>
                <input type="text" id="csv-columns" name="columns" placeholder="name, email, date">
            </div>
            <div class="form-group">
                <label for="csv-delimiter">Delimiter</label>
                <input type="text" id="csv-delimiter" name="delimiter" value="," maxlength="2">
            </div>
            <div class="form-group">
                <label for="csv-enclosure">Enclosure</label>
                <input type="text" id="csv-enclosure" name="enclosure" value="&quot;" maxlength="2">
            </div>
        </div>
        <div id="edit-convertcase-fields" class="edit-step-fields" style="display: none;">
            <div class="form-group">
                <label for="convertcase-columns">Columns to convert <span class="required">*</span></label>
                <input type="text" id="convertcase-columns" name="convertcaseColumns" placeholder="name, email, title">
            </div>
            <div class="form-group">
                <label for="convertcase-mode">Mode</label>
                <select id="convertcase-mode" name="convertcaseMode">
                    <option value="lower">Lower</option>
                    <option value="upper">Upper</option>
                    <option value="title">Title</option>
                </select>
            </div>
            <div class="form-group">
                <label for="convertcase-encoding">Encoding</label>
                <input type="text" id="convertcase-encoding" name="convertcaseEncoding" value="UTF-8" placeholder="UTF-8">
            </div>
        </div>
        <div id="edit-rename-fields" class="edit-step-fields" style="display: none;">
            <div class="form-group">
                <label for="rename-mappings">Column mappings <span class="required">*</span></label>
                <textarea id="rename-mappings" name="renameMappings" rows="5" placeholder="old_name -> new_name&#10;email -> email_address"></textarea>
            </div>
            <p class="edit-hint">
                One mapping per line. Use <code>old_name -&gt; new_name</code> or <code>old_name = new_name</code>.
            </p>
        </div>
        <div id="edit-database-fields" class="edit-step-fields" style="display: none;">
            <div class="form-group">
                <label for="db-driver">Driver</label>
                <select id="db-driver" name="dbDriver">
                    <option value="pdo_mysql">MySQL</option>
                    <option value="pdo_pgsql">PostgreSQL</option>
                    <option value="pdo_sqlite">SQLite</option>
                </select>
            </div>
            <div id="db-server-fields">
                <div class="form-group">
                    <label for="db-host">Host <span class="required">*</span></label>
                    <input type="text" id="db-host" name="dbHost" placeholder="localhost">
                </div>
                <div class="form-group">
                    <label for="db-port">Port</label>
                    <input type="number" id="db-port" name="dbPort" value="3306" min="1" max="65535">
                </div>
                <div class="form-group">
                    <label for="db-database">Database <span class="required">*</span></label>
                    <input type="text" id="db-database" name="dbDatabase" placeholder="mydb">
                </div>
                <div class="form-group">
                    <label for="db-user">User <span class="required">*</span></label>
                    <input type="text" id="db-user" name="dbUser" placeholder="root">
                </div>
                <div class="form-group">
                    <label for="db-password">Password</label>
                    <input type="password" id="db-password" name="dbPassword" placeholder="(optional for some setups)" autocomplete="off">
                </div>
            </div>
            <div id="db-sqlite-fields" style="display: none;">
                <div class="form-group">
                    <label for="db-path">Path to database file <span class="required">*</span></label>
                    <input type="text" id="db-path" name="dbPath" placeholder="/path/to/database.sqlite">
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="db-memory" name="dbMemory" value="1"> In-memory database
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="db-table">Table <span class="required">*</span></label>
                <input type="text" id="db-table" name="dbTable" placeholder="people">
            </div>
        </div>
        <div id="edit-other-fields" class="edit-step-fields" style="display: none;">
            <p class="edit-not-implemented">Edit not yet implemented for this step type.</p>
        </div>
    </form>
</div>

<script>
    function dragStart(event) {
        event.dataTransfer.setData('stepId', event.target.id);

        let stepType = event.target.dataset.stepType;
        event.dataTransfer.setData('stepType', stepType);
    }

    function allowDrop(event) {
        event.preventDefault();
    }

    function drop(event) {
        event.preventDefault();

        let bounds = event.target.getBoundingClientRect();

        x = event.clientX - bounds.left;
        y = event.clientY - bounds.top;

        let stepId = event.dataTransfer.getData('stepId');
        let stepType = event.dataTransfer.getData('stepType');

        addItem(stepId, stepType);
    }

    let flowchartCount = 3;

    let STEP_TYPE_EXTRACTOR = 'extractor';
    let STEP_TYPE_EXECUTION_EXTRACTOR = 'execution-extractor';
    let STEP_TYPE_TRANSFORMER = 'transformer';
    let STEP_TYPE_LOADER = 'loader';

    let steps = new Map();

    /** Per-node configuration: numericId -> { stepId, stepType, options } (used when building pipeline / running). */
    let nodeConfig = {};

    const STEPS_API_URL = 'http://localhost:8181/api/v1/steps';

    const STEP_OBJECT_CSV_EXTRACTOR = 'CsvExtractor';
    const STEP_OBJECT_CONVERT_CASE_TRANSFORMER = 'ConvertCaseTransformer';
    const STEP_OBJECT_RENAME_COLUMNS_TRANSFORMER = 'RenameColumnsTransformer';
    const STEP_OBJECT_DATABASE_LOADER = 'DatabaseLoader';

    function loadStepsFromApi() {
        $.ajax({
            url: STEPS_API_URL,
            method: 'GET',
            dataType: 'json',
            xhrFields: { withCredentials: true }
        }).done(function (data) {
            steps.clear();
            if (Array.isArray(data)) {
                data.forEach(function (s) {
                    steps.set(s.id, {
                        type: s.type,
                        name: s.name,
                        object: s.object,
                        description: s.description || ''
                    });
                });
            }
            populateStepsMenu();
        }).fail(function () {
            $('#left-container').html('<p class="steps-error">Could not load steps. Check the API is running.</p>');
        });
    }

    function getCoreStepName(step) {
        if (!step || !step.object) {
            return '';
        }
        let objectName = step.object;
        if (objectName.endsWith('Extractor')) {
            return objectName.replace(/Extractor$/, '');
        }
        if (objectName.endsWith('Transformer')) {
            return objectName.replace(/Transformer$/, '');
        }
        if (objectName.endsWith('Loader')) {
            return objectName.replace(/Loader$/, '');
        }
        return objectName;
    }

    /**
     * Order step nodes by connection flow (arrows) instead of creation order.
     * Uses topological sort: start from nodes with no incoming edges, then follow outgoing edges.
     * Returns array of numericIds in execution order, or null to fall back to numericId sort.
     */
    function getOrderedNumericIdsByConnections(nodeIds, connections) {
        if (!connections || connections.length === 0) {
            return null;
        }
        let inDegree = {};
        let successors = {};
        nodeIds.forEach(function (id) {
            inDegree[id] = 0;
            successors[id] = [];
        });
        connections.forEach(function (conn) {
            let srcId = (conn.source && conn.source.id) ? conn.source.id : (conn.sourceId || '');
            let tgtId = (conn.target && conn.target.id) ? conn.target.id : (conn.targetId || '');
            let srcNum = parseInt(String(srcId).replace(/^flowchartWindow/, ''), 10);
            let tgtNum = parseInt(String(tgtId).replace(/^flowchartWindow/, ''), 10);
            if (!isNaN(srcNum) && !isNaN(tgtNum) && nodeIds.indexOf(srcNum) !== -1 && nodeIds.indexOf(tgtNum) !== -1) {
                successors[srcNum].push(tgtNum);
                inDegree[tgtNum] = (inDegree[tgtNum] || 0) + 1;
            }
        });
        let queue = nodeIds.filter(function (id) { return inDegree[id] === 0; }).sort(function (a, b) { return a - b; });
        let order = [];
        while (queue.length > 0) {
            let n = queue.shift();
            order.push(n);
            (successors[n] || []).forEach(function (s) {
                inDegree[s]--;
                if (inDegree[s] === 0) {
                    queue.push(s);
                }
            });
            queue.sort(function (a, b) { return a - b; });
        }
        if (order.length !== nodeIds.length) {
            return null;
        }
        return order;
    }

    function buildStepsPayload() {
        let nodes = [];

        $('.flowchart-demo .window').each(function () {
            let elementId = this.id || '';
            if (!elementId || elementId.indexOf('flowchartWindow') !== 0) {
                return;
            }
            let numericId = parseInt(elementId.replace('flowchartWindow', ''), 10);
            if (isNaN(numericId)) {
                return;
            }
            let $el = $(this);
            let stepId = $el.data('step-id');
            if (!stepId) {
                return;
            }
            let stepDef = steps.get(stepId);
            if (!stepDef) {
                return;
            }
            let cfg = nodeConfig[numericId] || {
                stepId: stepId,
                stepType: stepDef.type,
                object: stepDef.object,
                options: {}
            };
            let coreName = getCoreStepName(stepDef);
            nodes.push({
                numericId: numericId,
                type: stepDef.type,
                name: coreName,
                options: cfg.options || {}
            });
        });

        let orderedIds = null;
        let jsp = window.jsp;
        if (jsp && typeof jsp.getConnections === 'function') {
            let connections = jsp.getConnections({}, true);
            let nodeIds = nodes.map(function (n) { return n.numericId; });
            orderedIds = getOrderedNumericIdsByConnections(nodeIds, connections);
        }
        if (orderedIds) {
            let byId = {};
            nodes.forEach(function (n) { byId[n.numericId] = n; });
            nodes = orderedIds.map(function (id) { return byId[id]; }).filter(Boolean);
        } else {
            nodes.sort(function (a, b) {
                return a.numericId - b.numericId;
            });
        }

        return nodes.map(function (n) {
            return {
                type: n.type,
                name: n.name,
                options: n.options
            };
        });
    }

    function saveTransformation() {
        let stepsPayload = buildStepsPayload();
        if (!stepsPayload.length) {
            alert('There are no steps on the canvas to save.');
            return;
        }

        let name = window.prompt('Transformation name (optional):');
        if (name !== null) {
            name = name.trim();
        }

        let payload = { steps: stepsPayload };
        if (name) {
            payload.name = name;
        }

        $.ajax({
            url: 'http://localhost:8181/api/v1/transformation',
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(payload),
            xhrFields: { withCredentials: true }
        }).done(function (response) {
            let data = typeof response === 'string' ? JSON.parse(response) : response;
            let msg = 'Transformation saved.';
            if (data && data.name) {
                msg = 'Transformation "' + data.name + '" saved.';
            }
            alert(msg);
        }).fail(function (xhr) {
            let msg = 'Failed to save transformation.';
            if (xhr && xhr.responseText) {
                try {
                    let err = JSON.parse(xhr.responseText);
                    if (typeof err === 'string') {
                        msg = err;
                    } else if (err && err.message) {
                        msg = err.message;
                    }
                } catch (e) {
                    // ignore parse errors
                }
            }
            alert(msg);
        });
    }

    function getMenuStepName(stepType, stepName) {
        let fullStepName = '';

        if (stepType === STEP_TYPE_EXTRACTOR) {
            fullStepName = stepName + ' Extractor';
        }

        if (stepType === STEP_TYPE_EXECUTION_EXTRACTOR) {
            fullStepName = stepName + ' Execution Extractor';
        }

        if (stepType === STEP_TYPE_TRANSFORMER) {
            fullStepName = stepName + ' Transformer';
        }

        if (stepType === STEP_TYPE_LOADER) {
            fullStepName = stepName + ' Loader';
        }

        return fullStepName
    }

    function populateStepsMenu() {
        $('#left-container').empty();

        const groups = [
            { type: STEP_TYPE_EXTRACTOR, label: 'Extractors' },
            { type: STEP_TYPE_EXECUTION_EXTRACTOR, label: 'Execution extractors' },
            { type: STEP_TYPE_TRANSFORMER, label: 'Transformers' },
            { type: STEP_TYPE_LOADER, label: 'Loaders' }
        ];

        groups.forEach(group => {
            let hasAny = false;
            steps.forEach((value, key) => {
                if (value.type !== group.type) {
                    return;
                }

                if (!hasAny) {
                    $('<div>', {
                        class: 'steps-group-title'
                    }).text(group.label).appendTo('#left-container');
                    hasAny = true;
                }

                let attributes = {
                    id: key,
                    class: 'draggable-item',
                    'data-step-type': value.type,
                    ondragstart: 'dragStart(event)',
                    title: value.description || ''
                };

                let stepName = value.name;

                $('<p>', attributes).html(stepName).appendTo('#left-container');

                let currentStep = document.getElementById(key);
                currentStep.setAttribute('draggable', true);
            });
        });
    }

    function getExtractorCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        let titleAttr = step.description ? ' title="' + step.description.replace(/"/g, '&quot;') + '"' : '';

        stepHtml = '';
        stepHtml += '<p style="text-align: center"' + titleAttr + '>';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Extractor</label>';
        stepHtml += '<br/>';
        stepHtml += '<span class="step-actions">';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');" class="step-icon" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');" class="step-icon" title="Remove" aria-label="Remove">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '</span>';
        stepHtml += '</p>';

        return stepHtml;
    }

    function getExecutionExtractorCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        let titleAttr = step.description ? ' title="' + step.description.replace(/"/g, '&quot;') + '"' : '';

        stepHtml = '';
        stepHtml += '<p style="text-align: center"' + titleAttr + '>';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Execution Extractor</label>';
        stepHtml += '<br/>';
        stepHtml += '<span class="step-actions">';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');" class="step-icon" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');" class="step-icon" title="Remove" aria-label="Remove">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '</span>';
        stepHtml += '</p>';

        return stepHtml;
    }

    function getTransformerCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        let titleAttr = step.description ? ' title="' + step.description.replace(/"/g, '&quot;') + '"' : '';

        stepHtml = '';
        stepHtml += '<p style="text-align: center"' + titleAttr + '>';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Transformer</label>';
        stepHtml += '<br/>';
        stepHtml += '<span class="step-actions">';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');" class="step-icon" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');" class="step-icon" title="Remove" aria-label="Remove">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '</span>';
        stepHtml += '</p>';

        return stepHtml;
    }

    function getLoaderCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        let titleAttr = step.description ? ' title="' + step.description.replace(/"/g, '&quot;') + '"' : '';

        stepHtml = '';
        stepHtml += '<p style="text-align: center"' + titleAttr + '>';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Loader</label>';
        stepHtml += '<br/>';
        stepHtml += '<span class="step-actions">';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');" class="step-icon" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');" class="step-icon" title="Remove" aria-label="Remove">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '</span>';
        stepHtml += '</p>';

        return stepHtml;
    }

    function getStepHtml(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepType = step.type;

        if (stepType === STEP_TYPE_EXTRACTOR) {
            stepHtml = getExtractorCode(stepId, numericId);
        }

        if (stepType === STEP_TYPE_EXECUTION_EXTRACTOR) {
            stepHtml = getExecutionExtractorCode(stepId, numericId);
        }

        if (stepType === STEP_TYPE_TRANSFORMER) {
            stepHtml = getTransformerCode(stepId, numericId);
        }

        if (stepType === STEP_TYPE_LOADER) {
            stepHtml = getLoaderCode(stepId, numericId);
        }

        return stepHtml;
    }

    function editStep(numericId) {
        let elementId = 'flowchartWindow' + numericId;
        let element = $('#' + elementId);
        let stepId = element.data('step-id');
        let step = steps.get(stepId);
        if (!step) {
            return;
        }
        $('#edit-numeric-id').val(numericId);
        if (step.description) {
            $('#step-description-text').text(step.description).show();
        } else {
            $('#step-description-text').hide().text('');
        }
        $('#edit-csv-fields').hide();
        $('#edit-convertcase-fields').hide();
        $('#edit-rename-fields').hide();
        $('#edit-database-fields').hide();
        $('#edit-other-fields').hide();
        if (step.object === STEP_OBJECT_CSV_EXTRACTOR) {
            $('#edit-csv-fields').show();
            let cfg = nodeConfig[numericId] || {};
            let opts = cfg.options || {};
            $('#csv-file-path').val(opts.filePath || '');
            $('#csv-columns').val(Array.isArray(opts.columns) ? opts.columns.join(', ') : (opts.columns || ''));
            $('#csv-delimiter').val(opts.delimiter !== undefined ? opts.delimiter : ',');
            $('#csv-enclosure').val(opts.enclosure !== undefined ? opts.enclosure : '"');
            $('#step-edit-modal').dialog('option', 'title', 'Edit CSV Extractor');
            $('#step-edit-modal').dialog('open');
        } else if (step.object === STEP_OBJECT_CONVERT_CASE_TRANSFORMER) {
            $('#edit-convertcase-fields').show();
            let cfg = nodeConfig[numericId] || {};
            let opts = cfg.options || {};
            $('#convertcase-columns').val(Array.isArray(opts.columns) ? opts.columns.join(', ') : (opts.columns || ''));
            var convertCaseModeVal = 'lower';
if (opts.mode !== undefined) {
    if (typeof opts.mode === 'string') convertCaseModeVal = opts.mode.toLowerCase();
    else if (opts.mode === 1) convertCaseModeVal = 'upper';
    else if (opts.mode === 2) convertCaseModeVal = 'lower';
    else if (opts.mode === 3) convertCaseModeVal = 'title';
}
$('#convertcase-mode').val(convertCaseModeVal);
            $('#convertcase-encoding').val(opts.encoding || 'UTF-8');
            $('#step-edit-modal').dialog('option', 'title', 'Edit Convert Case Transformer');
            $('#step-edit-modal').dialog('open');
        } else if (step.object === STEP_OBJECT_RENAME_COLUMNS_TRANSFORMER) {
            $('#edit-rename-fields').show();
            let cfg = nodeConfig[numericId] || {};
            let opts = cfg.options || {};
            let mappings = [];
            if (opts.columns && typeof opts.columns === 'object') {
                Object.keys(opts.columns).forEach(function (oldName) {
                    let newName = opts.columns[oldName];
                    if (oldName && newName) {
                        mappings.push(oldName + ' -> ' + newName);
                    }
                });
            }
            $('#rename-mappings').val(mappings.join('\n'));
            $('#step-edit-modal').dialog('option', 'title', 'Edit Rename Columns Transformer');
            $('#step-edit-modal').dialog('open');
        } else if (step.object === STEP_OBJECT_DATABASE_LOADER) {
            $('#edit-database-fields').show();
            let cfg = nodeConfig[numericId] || {};
            let opts = cfg.options || {};
            let driver = opts.driver || 'pdo_mysql';
            $('#db-driver').val(driver);
            toggleDatabaseDriverFields(driver);
            $('#db-host').val(opts.host || 'localhost');
            $('#db-port').val(opts.port !== undefined ? opts.port : (driver === 'pdo_pgsql' ? 5432 : 3306));
            $('#db-database').val(opts.database || opts.dbName || '');
            $('#db-table').val(opts.table || opts.tableName || '');
            $('#db-user').val(opts.user || '');
            $('#db-password').val(opts.password || '');
            $('#db-path').val(opts.path || '');
            $('#db-memory').prop('checked', !!opts.memory);
            $('#step-edit-modal').dialog('option', 'title', 'Edit Database Loader');
            $('#step-edit-modal').dialog('open');
        } else {
            $('#edit-other-fields').show();
            $('#step-edit-modal').dialog('option', 'title', 'Edit ' + (step.name || 'step'));
            $('#step-edit-modal').dialog('open');
        }
    }

    function saveStepEdit() {
        let numericId = $('#edit-numeric-id').val();
        if (!numericId) return;
        let elementId = 'flowchartWindow' + numericId;
        let element = $('#' + elementId);
        let stepId = element.data('step-id');
        let step = steps.get(stepId);
        if (!step) {
            $('#step-edit-modal').dialog('close');
            return;
        }
        if (step.object === STEP_OBJECT_CSV_EXTRACTOR) {
            let filePath = $('#csv-file-path').val().trim();
            if (!filePath) {
                alert('File path is required.');
                return;
            }
            let columnsStr = $('#csv-columns').val().trim();
            let columns = columnsStr ? columnsStr.split(',').map(function (s) { return s.trim(); }).filter(Boolean) : [];
            let delimiter = $('#csv-delimiter').val().trim() || ',';
            let enclosure = $('#csv-enclosure').val().trim();
            if (enclosure === '') enclosure = '"';
            nodeConfig[numericId] = {
                stepId: stepId,
                stepType: step.type,
                object: step.object,
                options: {
                    filePath: filePath,
                    columns: columns,
                    delimiter: delimiter,
                    enclosure: enclosure
                }
            };
        } else if (step.object === STEP_OBJECT_CONVERT_CASE_TRANSFORMER) {
            let columnsStr = $('#convertcase-columns').val().trim();
            if (!columnsStr) {
                alert('At least one column is required.');
                return;
            }
            let columns = columnsStr.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
            let mode = $('#convertcase-mode').val();
            let encoding = $('#convertcase-encoding').val().trim() || 'UTF-8';
            nodeConfig[numericId] = {
                stepId: stepId,
                stepType: step.type,
                object: step.object,
                options: {
                    columns: columns,
                    mode: mode,
                    encoding: encoding
                }
            };
        } else if (step.object === STEP_OBJECT_RENAME_COLUMNS_TRANSFORMER) {
            let raw = $('#rename-mappings').val().trim();
            if (!raw) {
                alert('At least one column mapping is required.');
                return;
            }
            let lines = raw.split('\n');
            let columnsMap = {};
            lines.forEach(function (line) {
                let text = line.trim();
                if (!text) {
                    return;
                }
                let parts = text.split('->');
                if (parts.length < 2) {
                    parts = text.split('=');
                }
                let from = parts[0] ? parts[0].trim() : '';
                let to = parts[1] ? parts[1].trim() : '';
                if (from && to) {
                    columnsMap[from] = to;
                }
            });
            if (Object.keys(columnsMap).length === 0) {
                alert('Could not parse any valid mappings. Use \"old_name -> new_name\" per line.');
                return;
            }
            nodeConfig[numericId] = {
                stepId: stepId,
                stepType: step.type,
                object: step.object,
                options: {
                    columns: columnsMap
                }
            };
        } else if (step.object === STEP_OBJECT_DATABASE_LOADER) {
            let driver = $('#db-driver').val();
            let table = $('#db-table').val().trim();
            if (!table) {
                alert('Table is required.');
                return;
            }
            let options = { driver: driver, tableName: table };
            if (driver === 'pdo_sqlite') {
                let memory = $('#db-memory').prop('checked');
                let path = $('#db-path').val().trim();
                if (!memory && !path) {
                    alert('Path is required (or check In-memory database).');
                    return;
                }
                options.path = memory ? '' : path;
                options.memory = memory;
            } else {
                let host = $('#db-host').val().trim();
                let database = $('#db-database').val().trim();
                let user = $('#db-user').val().trim();
                if (!host || !database || !user) {
                    alert('Host, Database, and User are required.');
                    return;
                }
                let port = parseInt($('#db-port').val(), 10) || (driver === 'pdo_pgsql' ? 5432 : 3306);
                options.host = host;
                options.port = port;
                options.database = database;
                options.user = user;
                options.password = $('#db-password').val() || '';
            }
            nodeConfig[numericId] = {
                stepId: stepId,
                stepType: step.type,
                object: step.object,
                options: options
            };
        } else {
            $('#step-edit-modal').dialog('close');
            return;
        }
        $('#step-edit-modal').dialog('close');
    }

    function remove(numericId) {
        if (confirm('Do you confirm removing this step?')) {
            let elementId = 'flowchartWindow' + numericId;

            instance.deleteConnectionsForElement(elementId);
            instance.remove(elementId);
            delete nodeConfig[numericId];
        }
    }

    function getConnectionSetup(stepType) {
        let outgoingConnections = [];
        let incomingConnections = [];

        if (stepType === STEP_TYPE_EXTRACTOR) {
            outgoingConnections = ['RightMiddle'];
            incomingConnections = [];
        }

        if (stepType === STEP_TYPE_EXECUTION_EXTRACTOR) {
            outgoingConnections = ['RightMiddle'];
            incomingConnections = ['LeftMiddle'];
        }

        if (stepType === STEP_TYPE_TRANSFORMER) {
            outgoingConnections = ['RightMiddle'];
            incomingConnections = ['LeftMiddle'];
        }

        if (stepType === STEP_TYPE_LOADER) {
            outgoingConnections = [];
            incomingConnections = ['LeftMiddle'];
        }

        return {'outgoingConnections': outgoingConnections, 'incomingConnections': incomingConnections};
    }

    function addItem(stepId, stepType) {
        flowchartCount++;

        let idAttribute = 'flowchartWindow' + flowchartCount;
        let classAttribute = 'window jtk-node';
        let cssAttribute = {top: y, left: x};

        let attributes = {id: idAttribute, class: classAttribute, css: cssAttribute, 'data-step-id': stepId};

        $('<div>', attributes).html(getStepHtml(stepId, flowchartCount)).appendTo('#canvas');

        let connectionSetup = getConnectionSetup(stepType);

        instance._addEndpoints('Window' + flowchartCount, connectionSetup.outgoingConnections, connectionSetup.incomingConnections);

        instance.draggable(instance.getSelector('.flowchart-demo .window'), {grid: [20, 20]});
    }

    let x = undefined;
    let y = undefined;

    function relativeCoords(event) {
        let bounds = event.target.getBoundingClientRect();

        x = event.clientX - bounds.left;
        y = event.clientY - bounds.top;
    }

    function toggleDatabaseDriverFields(driver) {
        if (driver === 'pdo_sqlite') {
            $('#db-server-fields').hide();
            $('#db-sqlite-fields').show();
        } else {
            $('#db-server-fields').show();
            $('#db-sqlite-fields').hide();
            $('#db-port').val(driver === 'pdo_pgsql' ? 5432 : 3306);
        }
    }

    function initStepEditDialog() {
        $('#step-edit-modal').dialog({
            autoOpen: false,
            modal: true,
            width: 420,
            buttons: {
                Save: function () {
                    saveStepEdit();
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#db-driver').on('change', function () {
            toggleDatabaseDriverFields($(this).val());
        });
        $('#save-transformation-btn').on('click', function () {
            saveTransformation();
        });
    }

    (function () {
        let diagramContainer = document.getElementById('diagram-container');
        diagramContainer.addEventListener('mousemove', relativeCoords, false);

        initStepEditDialog();
        loadStepsFromApi();
    })();
</script>
</body>
</html>
