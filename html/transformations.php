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
                    <option value="2">Lower</option>
                    <option value="1">Upper</option>
                    <option value="3">Title</option>
                </select>
            </div>
            <div class="form-group">
                <label for="convertcase-encoding">Encoding</label>
                <input type="text" id="convertcase-encoding" name="convertcaseEncoding" value="UTF-8" placeholder="UTF-8">
            </div>
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
                    steps.set(s.id, { type: s.type, name: s.name, object: s.object });
                });
            }
            populateStepsMenu();
        }).fail(function () {
            $('#left-container').html('<p class="steps-error">Could not load steps. Check the API is running.</p>');
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
        steps.forEach(
            (value, key) => {
                let attributes = {
                    id: key,
                    class: 'draggable-item',
                    'data-step-type': value.type,
                    ondragstart: 'dragStart(event)'
                };

                let stepName = getMenuStepName(value.type, value.name);

                $('<p>', attributes).html(stepName).appendTo('#left-container');

                let currentStep = document.getElementById(key);
                currentStep.setAttribute('draggable', true);
            }
        )
    }

    function getExtractorCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        stepHtml = '';
        stepHtml += '<p style="text-align: center">';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Extractor</label>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');">Edit</a>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');">Remove</a>';
        stepHtml += '</p>';

        return stepHtml;
    }

    function getExecutionExtractorCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        stepHtml = '';
        stepHtml += '<p style="text-align: center">';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Execution Extractor</label>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');">Edit</a>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');">Remove</a>';
        stepHtml += '</p>';

        return stepHtml;
    }

    function getTransformerCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        stepHtml = '';
        stepHtml += '<p style="text-align: center">';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Transformer</label>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');">Edit</a>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');">Remove</a>';
        stepHtml += '</p>';

        return stepHtml;
    }

    function getLoaderCode(stepId, numericId) {
        let stepHtml = '';

        let step = steps.get(stepId);
        let stepName = step.name;

        stepHtml = '';
        stepHtml += '<p style="text-align: center">';
        stepHtml += '<strong>' + stepName + '</strong>';
        stepHtml += '<br/>';
        stepHtml += '<label>Loader</label>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:editStep(' + numericId + ');">Edit</a>';
        stepHtml += '<br/>';
        stepHtml += '<a href="javascript:remove(' + numericId + ');">Remove</a>';
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
        $('#edit-csv-fields').hide();
        $('#edit-convertcase-fields').hide();
        $('#edit-database-fields').hide();
        $('#edit-other-fields').hide();
        if (step.object === STEP_OBJECT_CSV_EXTRACTOR) {
            $('#edit-csv-fields').show();
            let cfg = nodeConfig[numericId] || {};
            $('#csv-file-path').val(cfg.filePath || '');
            $('#csv-columns').val(Array.isArray(cfg.columns) ? cfg.columns.join(', ') : (cfg.columns || ''));
            $('#csv-delimiter').val(cfg.delimiter !== undefined ? cfg.delimiter : ',');
            $('#csv-enclosure').val(cfg.enclosure !== undefined ? cfg.enclosure : '"');
            $('#step-edit-modal').dialog('option', 'title', 'Edit CSV Extractor');
            $('#step-edit-modal').dialog('open');
        } else if (step.object === STEP_OBJECT_CONVERT_CASE_TRANSFORMER) {
            $('#edit-convertcase-fields').show();
            let cfg = nodeConfig[numericId] || {};
            let opts = cfg.options || {};
            $('#convertcase-columns').val(Array.isArray(opts.columns) ? opts.columns.join(', ') : (opts.columns || ''));
            $('#convertcase-mode').val(opts.mode !== undefined ? String(opts.mode) : '2');
            $('#convertcase-encoding').val(opts.encoding || 'UTF-8');
            $('#step-edit-modal').dialog('option', 'title', 'Edit Convert Case Transformer');
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
            let mode = parseInt($('#convertcase-mode').val(), 10);
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
