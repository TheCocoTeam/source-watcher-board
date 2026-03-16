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
            <label for="saved-transformations-select" class="top-menu-label">Transformations:</label>
            <select id="saved-transformations-select" class="top-menu-select" title="List of saved transformations">
                <option value="">—</option>
            </select>
            <button type="button" id="load-transformation-btn" class="top-menu-button">Load</button>
            <button type="button" id="run-transformation-btn" class="top-menu-button">Run saved</button>
            <button type="button" id="run-current-btn" class="top-menu-button">Run current</button>
            <button type="button" id="save-transformation-btn" class="top-menu-button">Save</button>
            <a href="login.php" id="logout-btn">Log out</a>
        </div>

        <div id="central-container">
            <aside class="sidebar-panel">
                <h2 class="sidebar-title">Steps</h2>
                <p class="sidebar-hint">Drag a step onto the canvas</p>
                <div id="left-container"></div>
            </aside>

            <div id="diagram-container">
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
        <div id="edit-json-fields" class="edit-step-fields" style="display: none;">
            <div class="form-group">
                <label for="json-file-path">File path <span class="required">*</span></label>
                <input type="text" id="json-file-path" name="jsonFilePath" placeholder="/path/to/file.json or https://...">
            </div>
            <div class="form-group">
                <label for="json-columns">JSONPath mappings (optional)</label>
                <textarea id="json-columns" name="jsonColumns" rows="5" placeholder="columnName: jsonPath&#10;color: colors.*.color"></textarea>
            </div>
            <p class="edit-hint">
                One mapping per line: <code>columnName: jsonPath</code>. Leave empty to use the root JSON as rows. Example: <code>color: colors.*.color</code>
            </p>
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
        var dt = (event.originalEvent && event.originalEvent.dataTransfer) || event.dataTransfer;
        if (!dt) {
            return;
        }
        let el = (event.target && event.target.closest) ? event.target.closest('.draggable-item') : event.target;
        let stepId = el && el.id ? el.id : '';
        let stepType = el && el.dataset && el.dataset.stepType ? el.dataset.stepType : '';
        dt.setData('stepId', stepId);
        dt.setData('stepType', stepType);
        dt.setData('text/plain', stepId + '\t' + stepType);
    }

    function allowDrop(event) {
        event.preventDefault();
    }

    function drop(event) {
        event.preventDefault();

        let bounds = event.target.getBoundingClientRect();

        x = event.clientX - bounds.left;
        y = event.clientY - bounds.top;

        let stepId = '';
        let stepType = '';
        var dt = (event.originalEvent && event.originalEvent.dataTransfer) || event.dataTransfer;
        if (dt) {
            stepId = dt.getData('stepId') || '';
            stepType = dt.getData('stepType') || '';
            if (!stepId || !stepType) {
                let plain = dt.getData('text/plain');
                if (plain) {
                    let parts = plain.split('\t');
                    if (parts.length >= 2) {
                        stepId = parts[0];
                        stepType = parts[1];
                    }
                }
            }
        }

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
    const TRANSFORMATION_API_URL = 'http://localhost:8181/api/v1/transformation';
    const TRANSFORMATION_RUN_API_URL = 'http://localhost:8181/api/v1/transformation-run';

    const STEP_OBJECT_CSV_EXTRACTOR = 'CsvExtractor';
    const STEP_OBJECT_JSON_EXTRACTOR = 'JsonExtractor';
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

    function loadSavedTransformationsList() {
        $.ajax({
            url: TRANSFORMATION_API_URL,
            method: 'GET',
            dataType: 'json',
            xhrFields: { withCredentials: true }
        }).done(function (data) {
            let names = (data && Array.isArray(data.names)) ? data.names : [];
            let $sel = $('#saved-transformations-select');
            let current = $sel.val() || loadedTransformationName || '';
            $sel.find('option').not(':first').remove();
            names.forEach(function (name) {
                $sel.append($('<option>', { value: name }).text(name));
            });
            if (current && names.indexOf(current) !== -1) {
                $sel.val(current);
            }
        }).fail(function () {
            $('#saved-transformations-select').find('option').not(':first').remove();
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

    /** Find step id from steps Map by type and core name (as stored in .swt). */
    function getStepIdByTypeAndName(type, name) {
        let nameStr = (name || '').toString();
        for (let [stepId, step] of steps) {
            if (step.type !== type) continue;
            if (getCoreStepName(step) === nameStr) return stepId;
        }
        return null;
    }

    function clearCanvas() {
        let jsp = window.jsp;
        let ids = [];
        $('.flowchart-demo .window').each(function () {
            let id = this.id;
            if (id && id.indexOf('flowchartWindow') === 0) ids.push(id);
        });
        ids.forEach(function (id) {
            if (jsp && typeof jsp.deleteConnectionsForElement === 'function') {
                jsp.deleteConnectionsForElement(id);
            }
            if (jsp && typeof jsp.remove === 'function') {
                jsp.remove(id);
            }
        });
        nodeConfig = {};
        flowchartCount = 0;
    }

    /** Add a single node at (x, y) with given stepId, stepType, and options (for nodeConfig). */
    function addNodeAt(stepId, stepType, x, y, options) {
        flowchartCount++;
        let numericId = flowchartCount;
        let idAttribute = 'flowchartWindow' + numericId;
        let cssAttribute = { top: y, left: x };
        let attributes = { id: idAttribute, class: 'window jtk-node', css: cssAttribute, 'data-step-id': stepId };
        $('<div>', attributes).html(getStepHtml(stepId, numericId)).appendTo('#canvas');
        let connectionSetup = getConnectionSetup(stepType);
        var jsp = window.jsp || window.instance;
        if (jsp && typeof jsp._addEndpoints === 'function') {
            jsp._addEndpoints('Window' + numericId, connectionSetup.outgoingConnections, connectionSetup.incomingConnections);
        }
        let stepDef = steps.get(stepId);
        nodeConfig[numericId] = {
            stepId: stepId,
            stepType: stepType,
            object: stepDef ? stepDef.object : '',
            options: options || {}
        };
        if (jsp && typeof jsp.draggable === 'function') {
            jsp.draggable(jsp.getSelector('.flowchart-demo .window'), { grid: [20, 20] });
        }
    }

    function loadTransformation(name) {
        if (!name || !name.trim()) return;
        $.ajax({
            url: TRANSFORMATION_API_URL + '?name=' + encodeURIComponent(name.trim()),
            method: 'GET',
            dataType: 'json',
            xhrFields: { withCredentials: true }
        }).done(function (data) {
            let stepsList = (data && data.steps) ? data.steps : (Array.isArray(data) ? data : null);
            if (!Array.isArray(stepsList) || stepsList.length === 0) {
                alert('Transformation has no steps.');
                return;
            }
            clearCanvas();
            let defaultX = 80, defaultY = 100, defaultSpacing = 180;
            for (let i = 0; i < stepsList.length; i++) {
                let step = stepsList[i];
                let type = (step.type || '').toString();
                let stepName = (step.name || '').toString();
                let stepId = getStepIdByTypeAndName(type, stepName);
                if (!stepId) {
                    alert('Unknown step type/name: ' + type + ' / ' + stepName + '. Load aborted.');
                    return;
                }
                let stepDef = steps.get(stepId);
                let stepType = stepDef ? stepDef.type : type;
                let x = (typeof step.x === 'number') ? step.x : (defaultX + i * defaultSpacing);
                let y = (typeof step.y === 'number') ? step.y : defaultY;
                let options = (step.options && typeof step.options === 'object') ? step.options : {};
                addNodeAt(stepId, stepType, x, y, options);
            }
            let jsp = window.jsp;
            if (jsp && typeof jsp.connect === 'function') {
                for (let i = 1; i < stepsList.length; i++) {
                    var sourceUuid = 'Window' + i + 'RightMiddle';
                    var targetUuid = 'Window' + (i + 1) + 'LeftMiddle';
                    jsp.connect({ uuids: [sourceUuid, targetUuid], detachable: true, editable: true });
                }
            }
            loadedTransformationName = name.trim();
        }).fail(function (xhr) {
            let msg = 'Failed to load transformation.';
            if (xhr && xhr.responseText) {
                try {
                    let err = JSON.parse(xhr.responseText);
                    if (err && err.message) msg = err.message;
                } catch (e) {}
            }
            alert(msg);
        });
    }

    let loadedTransformationName = '';

    function runTransformationSaved() {
        let name = $('#saved-transformations-select').val();
        if (!name) {
            alert('Select a transformation to run.');
            return;
        }
        if (!validateGraphBeforeRun()) {
            return;
        }
        let startedAt = (window.performance && performance.now) ? performance.now() : Date.now();
        let token = typeof getStoredAccessToken === 'function' ? getStoredAccessToken() : (sessionStorage.getItem('access_token') || localStorage.getItem('access_token'));
        $.ajax({
            url: TRANSFORMATION_RUN_API_URL,
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({ name: name }),
            xhrFields: { withCredentials: true },
            headers: token ? { 'x-access-token': token } : {}
        }).done(function (data) {
            let endedAt = (window.performance && performance.now) ? performance.now() : Date.now();
            let summary = buildRunSuccessMessage(data, startedAt, endedAt);
            $('#bottom-container').text(summary).css('color', '');
        }).fail(function (xhr) {
            let msg = formatRunError(xhr);
            $('#bottom-container').text(msg).css('color', '#c0392b');
            alert(msg);
        });
    }

    function formatRunError(xhr) {
        let msg = 'Run failed.';
        if (xhr && xhr.responseText) {
            try {
                let err = JSON.parse(xhr.responseText);
                if (err && err.message) msg = err.message;
                if (err && err.error) {
                    if (typeof err.stepIndex === 'number' && err.stepName) {
                        let stepNum = err.stepIndex + 1;
                        msg = 'Step ' + stepNum + ' (' + err.stepName + ') failed: ' + err.error;
                    } else {
                        msg += ' ' + err.error;
                    }
                }
            } catch (e) {}
        }
        return msg;
    }

    function runTransformationCurrent() {
        let stepsPayload = buildStepsPayload();
        if (!stepsPayload.length) {
            alert('There are no steps on the canvas to run.');
            return;
        }
        if (!validateGraphBeforeRun()) {
            return;
        }
        let startedAt = (window.performance && performance.now) ? performance.now() : Date.now();
        let token = typeof getStoredAccessToken === 'function' ? getStoredAccessToken() : (sessionStorage.getItem('access_token') || localStorage.getItem('access_token'));
        $.ajax({
            url: TRANSFORMATION_RUN_API_URL,
            method: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({ steps: stepsPayload }),
            xhrFields: { withCredentials: true },
            headers: token ? { 'x-access-token': token } : {}
        }).done(function (data) {
            let endedAt = (window.performance && performance.now) ? performance.now() : Date.now();
            let summary = buildRunSuccessMessage(data, startedAt, endedAt);
            $('#bottom-container').text(summary).css('color', '');
        }).fail(function (xhr) {
            let msg = formatRunError(xhr);
            $('#bottom-container').text(msg).css('color', '#c0392b');
            alert(msg);
        });
    }

    function validateGraphBeforeRun() {
        let nodes = [];
        $('.flowchart-demo .window').each(function () {
            let elementId = this.id || '';
            if (!elementId || elementId.indexOf('flowchartWindow') !== 0) return;
            let numericId = parseInt(elementId.replace('flowchartWindow', ''), 10);
            if (isNaN(numericId)) return;
            let stepId = $(this).data('step-id');
            if (!stepId) return;
            let stepDef = steps.get(stepId);
            if (!stepDef) return;
            nodes.push({ numericId: numericId, type: stepDef.type });
        });
        if (!nodes.length) {
            let msg = 'Cannot run: there are no steps on the canvas.';
            $('#bottom-container').text(msg).css('color', '#c0392b');
            alert(msg);
            return false;
        }
        let hasExtractor = nodes.some(function (n) { return n.type === STEP_TYPE_EXTRACTOR || n.type === STEP_TYPE_EXECUTION_EXTRACTOR; });
        let hasLoader = nodes.some(function (n) { return n.type === STEP_TYPE_LOADER; });
        if (!hasExtractor || !hasLoader) {
            let msg = 'Cannot run: you need at least one extractor and one loader.';
            $('#bottom-container').text(msg).css('color', '#c0392b');
            alert(msg);
            return false;
        }
        let jsp = window.jsp;
        if (!jsp || typeof jsp.getConnections !== 'function') {
            return true;
        }
        let connections = jsp.getConnections({}, true) || [];
        if (!connections.length) {
            let msg = 'Cannot run: no connections between steps. Connect steps before running.';
            $('#bottom-container').text(msg).css('color', '#c0392b');
            alert(msg);
            return false;
        }
        let nodeIds = nodes.map(function (n) { return n.numericId; });
        let adj = {};
        nodeIds.forEach(function (id) { adj[id] = []; });
        connections.forEach(function (conn) {
            let srcId = (conn.source && conn.source.id) ? conn.source.id : (conn.sourceId || '');
            let tgtId = (conn.target && conn.target.id) ? conn.target.id : (conn.targetId || '');
            let srcNum = parseInt(String(srcId).replace(/^flowchartWindow/, ''), 10);
            let tgtNum = parseInt(String(tgtId).replace(/^flowchartWindow/, ''), 10);
            if (!isNaN(srcNum) && !isNaN(tgtNum) && adj[srcNum]) {
                adj[srcNum].push(tgtNum);
            }
        });
        let extractorIds = nodes.filter(function (n) {
            return n.type === STEP_TYPE_EXTRACTOR || n.type === STEP_TYPE_EXECUTION_EXTRACTOR;
        }).map(function (n) { return n.numericId; });
        let loaderIds = nodes.filter(function (n) {
            return n.type === STEP_TYPE_LOADER;
        }).map(function (n) { return n.numericId; });
        let visited = {};
        let queue = [];
        extractorIds.forEach(function (id) {
            visited[id] = true;
            queue.push(id);
        });
        let reachableLoader = false;
        while (queue.length > 0 && !reachableLoader) {
            let current = queue.shift();
            if (loaderIds.indexOf(current) !== -1) {
                reachableLoader = true;
                break;
            }
            (adj[current] || []).forEach(function (next) {
                if (!visited[next]) {
                    visited[next] = true;
                    queue.push(next);
                }
            });
        }
        if (!reachableLoader) {
            let msg = 'Cannot run: no path from any extractor to any loader. Connect steps before running.';
            $('#bottom-container').text(msg).css('color', '#c0392b');
            alert(msg);
            return false;
        }
        return true;
    }

    function buildRunSuccessMessage(data, startedAt, endedAt) {
        let baseMsg = (data && data.message) ? data.message : 'Transformation ran successfully.';
        if (data && data.name) {
            baseMsg += ' (' + data.name + ')';
        }
        let elapsedMs = Math.max(0, (endedAt || 0) - (startedAt || 0));
        let elapsedSeconds = elapsedMs / 1000;
        let duration;
        if (elapsedSeconds < 60) {
            duration = elapsedSeconds.toFixed(2) + 's';
        } else {
            let minutes = Math.floor(elapsedSeconds / 60);
            let seconds = Math.round(elapsedSeconds - minutes * 60);
            duration = minutes + 'm ' + seconds + 's';
        }
        let now = new Date();
        let year = now.getUTCFullYear();
        let month = String(now.getUTCMonth() + 1).padStart(2, '0');
        let day = String(now.getUTCDate()).padStart(2, '0');
        let hour = String(now.getUTCHours()).padStart(2, '0');
        let minute = String(now.getUTCMinutes()).padStart(2, '0');
        let second = String(now.getUTCSeconds()).padStart(2, '0');
        let completedAt = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second + ' UTC';
        return baseMsg + ' • Completed at ' + completedAt + ' • Execution took ' + duration;
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
        let canvasEl = document.getElementById('canvas');
        let canvasRect = canvasEl ? canvasEl.getBoundingClientRect() : null;

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
            let x = 0, y = 0;
            if (canvasRect) {
                let nodeRect = this.getBoundingClientRect();
                x = Math.round(nodeRect.left - canvasRect.left);
                y = Math.round(nodeRect.top - canvasRect.top);
            }
            nodes.push({
                numericId: numericId,
                type: stepDef.type,
                name: coreName,
                options: cfg.options || {},
                x: x,
                y: y
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
            let step = {
                type: n.type,
                name: n.name,
                options: n.options
            };
            if (typeof n.x === 'number' && typeof n.y === 'number') {
                step.x = n.x;
                step.y = n.y;
            }
            return step;
        });
    }

    function saveTransformation() {
        let stepsPayload = buildStepsPayload();
        if (!stepsPayload.length) {
            alert('There are no steps on the canvas to save.');
            return;
        }

        let name = window.prompt('Transformation name (optional):', loadedTransformationName || '');
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
                loadedTransformationName = data.name;
            }
            alert(msg);
            loadSavedTransformationsList();
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
        stepHtml += '<a href="#" class="step-icon step-edit" data-numeric-id="' + numericId + '" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="#" class="step-icon step-remove" data-numeric-id="' + numericId + '" title="Remove" aria-label="Remove">';
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
        stepHtml += '<a href="#" class="step-icon step-edit" data-numeric-id="' + numericId + '" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="#" class="step-icon step-remove" data-numeric-id="' + numericId + '" title="Remove" aria-label="Remove">';
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
        stepHtml += '<a href="#" class="step-icon step-edit" data-numeric-id="' + numericId + '" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="#" class="step-icon step-remove" data-numeric-id="' + numericId + '" title="Remove" aria-label="Remove">';
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
        stepHtml += '<a href="#" class="step-icon step-edit" data-numeric-id="' + numericId + '" title="Edit" aria-label="Edit">';
        stepHtml += '<svg viewBox="0 0 24 24" width="14" height="14" role="img" aria-hidden="true">';
        stepHtml += '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42L18.37 3.29a1.003 1.003 0 0 0-1.42 0L15 5.25l3.75 3.75 1.96-1.96z"/>';
        stepHtml += '</svg></a>';
        stepHtml += '<a href="#" class="step-icon step-remove" data-numeric-id="' + numericId + '" title="Remove" aria-label="Remove">';
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
        if (!step || typeof step.type === 'undefined') {
            return '<p><strong>Unknown step</strong></p>';
        }
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
        $('#edit-json-fields').hide();
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
        } else if (step.object === STEP_OBJECT_JSON_EXTRACTOR) {
            $('#edit-json-fields').show();
            let cfg = nodeConfig[numericId] || {};
            let opts = cfg.options || {};
            $('#json-file-path').val(opts.filePath || '');
            let colLines = [];
            if (opts.columns && typeof opts.columns === 'object') {
                Object.keys(opts.columns).forEach(function (k) {
                    colLines.push(k + ': ' + opts.columns[k]);
                });
            }
            $('#json-columns').val(colLines.join('\n'));
            $('#step-edit-modal').dialog('option', 'title', 'Edit JSON Extractor');
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
        } else if (step.object === STEP_OBJECT_JSON_EXTRACTOR) {
            let filePath = $('#json-file-path').val().trim();
            if (!filePath) {
                alert('File path is required.');
                return;
            }
            let colText = $('#json-columns').val().trim();
            let columns = {};
            if (colText) {
                colText.split('\n').forEach(function (line) {
                    line = line.trim();
                    if (!line) return;
                    var idx = line.indexOf(':');
                    if (idx > 0) {
                        var colName = line.substring(0, idx).trim();
                        var path = line.substring(idx + 1).trim();
                        if (colName && path) columns[colName] = path;
                    }
                });
            }
            let jsonOpts = { filePath: filePath };
            if (Object.keys(columns).length > 0) jsonOpts.columns = columns;
            nodeConfig[numericId] = {
                stepId: stepId,
                stepType: step.type,
                object: step.object,
                options: jsonOpts
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
            var jsp = window.jsp || window.instance;
            if (jsp) {
                if (typeof jsp.deleteConnectionsForElement === 'function') jsp.deleteConnectionsForElement(elementId);
                if (typeof jsp.remove === 'function') jsp.remove(elementId);
            }
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
        if (!stepId || !steps.has(stepId)) {
            console.warn('addItem: missing or unknown stepId', stepId);
            alert('Could not add step: step data was lost or steps not loaded. Try again.');
            return;
        }
        if (!stepType) {
            let step = steps.get(stepId);
            stepType = step ? step.type : '';
        }
        if (!stepType) {
            alert('Could not add step: step type unknown.');
            return;
        }
        flowchartCount++;

        let idAttribute = 'flowchartWindow' + flowchartCount;
        let classAttribute = 'window jtk-node';
        let cssAttribute = {top: y, left: x};

        let attributes = {id: idAttribute, class: classAttribute, css: cssAttribute, 'data-step-id': stepId};

        $('<div>', attributes).html(getStepHtml(stepId, flowchartCount)).appendTo('#canvas');

        let connectionSetup = getConnectionSetup(stepType);
        var jsp = window.jsp || (typeof instance !== 'undefined' ? instance : null);
        var windowId = 'Window' + flowchartCount;
        if (jsp && typeof jsp._addEndpoints === 'function') {
            jsp._addEndpoints(windowId, connectionSetup.outgoingConnections, connectionSetup.incomingConnections);
        }
        if (jsp && typeof jsp.revalidate === 'function') {
            var elId = 'flowchartWindow' + flowchartCount;
            setTimeout(function () {
                if (jsp.revalidate) jsp.revalidate(elId);
            }, 0);
        }
        if (jsp && typeof jsp.draggable === 'function') {
            jsp.draggable(jsp.getSelector('.flowchart-demo .window'), {grid: [20, 20]});
        }
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
        $('#load-transformation-btn').on('click', function () {
            let name = $('#saved-transformations-select').val();
            if (name) {
                loadTransformation(name);
            } else {
                alert('Select a transformation to load.');
            }
        });
        $('#run-transformation-btn').on('click', function () {
            runTransformationSaved();
        });
        $('#run-current-btn').on('click', function () {
            runTransformationCurrent();
        });
        $(document).on('click', '.step-edit', function (e) {
            e.preventDefault();
            let id = parseInt($(this).data('numericId'), 10);
            if (!isNaN(id)) editStep(id);
        });
        $(document).on('click', '.step-remove', function (e) {
            e.preventDefault();
            let id = parseInt($(this).data('numericId'), 10);
            if (!isNaN(id)) remove(id);
        });
        $(document).on('dragstart', '.draggable-item', dragStart);
    }

    (function () {
        let diagramContainer = document.getElementById('diagram-container');
        if (diagramContainer) {
            diagramContainer.addEventListener('mousemove', relativeCoords, false);
            diagramContainer.addEventListener('dragover', allowDrop, false);
            diagramContainer.addEventListener('drop', drop, false);
        }

        initStepEditDialog();
        loadStepsFromApi();
        loadSavedTransformationsList();
    })();
</script>
</body>
</html>
