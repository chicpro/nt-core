<?php
require_once './_common.php';
require_once NT_PLUGIN_PATH.DIRECTORY_SEPARATOR.'menubuilder'.DIRECTORY_SEPARATOR.'menubuilder.php';

$html->setPageTitle(_('Menu'));

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.sub.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12"><h2><?php echo _('Menu'); ?></h2></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading clearfix"><h5 class="pull-left">Menu</h5>
                </div>
                <div class="panel-body" id="cont">
                    <ul id="myEditor" class="sortableLists list-group">
                    </ul>
                </div>
            </div>
            <div class="form-group">
                <button id="btnSave" type="button" class="btn btn-success"><i class="glyphicon glyphicon-ok"></i> Save</button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">Edit item</div>
                <div class="panel-body">
                    <form id="frmEdit" class="form-horizontal">
                        <div class="form-group">
                            <label for="text" class="col-sm-2 control-label">Text</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control item-menu" name="text" id="text" placeholder="Text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="href" class="col-sm-2 control-label">URL</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control item-menu" id="href" name="href" placeholder="URL">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="target" class="col-sm-2 control-label">Target</label>
                            <div class="col-sm-10">
                                <select name="target" id="target" class="form-control item-menu">
                                    <option value="_self">Self</option>
                                    <option value="_blank">Blank</option>
                                    <option value="_top">Top</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title" class="col-sm-2 control-label">Tooltip</label>
                            <div class="col-sm-10">
                                <input type="text" name="title" class="form-control item-menu" id="title" placeholder="Tooltip">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <button type="button" id="btnUpdate" class="btn btn-primary" disabled><i class="fa fa-refresh"></i> Update</button>
                    <button type="button" id="btnAdd" class="btn btn-success"><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'footer.sub.php';
?>