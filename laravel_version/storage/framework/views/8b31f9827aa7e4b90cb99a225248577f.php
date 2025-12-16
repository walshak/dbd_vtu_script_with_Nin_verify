<?php $__env->startSection('title', 'Network Settings'); ?>
<?php $__env->startSection('page-title', 'Network Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between mb-4">
    <a class="btn btn-dark me-2" href="<?php echo e(route('admin.system.settings')); ?>">General Setting</a>
    <a class="btn btn-primary me-2" href="<?php echo e(route('admin.system.contacts')); ?>">Contact Setting</a>
    <a class="btn btn-info" href="<?php echo e(route('admin.network-settings.index')); ?>">Network Setting</a>
</div>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header">
                <div class="d-flex justify-content-between align-items-center mt-2 network-logos">
                    <a class="me-3" href="<?php echo e(route('admin.network-settings.index', ['network' => 'MTN'])); ?>">
                        <img src="<?php echo e(asset('assets/images/mtn.png')); ?>"
                             class="img-fluid <?php echo e($network == 'MTN' ? 'border border-primary border-3' : ''); ?>"
                             style="width:80px;" alt="MTN" />
                    </a>
                    <a class="me-3" href="<?php echo e(route('admin.network-settings.index', ['network' => 'AIRTEL'])); ?>">
                        <img src="<?php echo e(asset('assets/images/airtel.png')); ?>"
                             class="img-fluid <?php echo e($network == 'AIRTEL' ? 'border border-primary border-3' : ''); ?>"
                             style="width:80px;" alt="Airtel" />
                    </a>
                    <a class="me-3" href="<?php echo e(route('admin.network-settings.index', ['network' => 'GLO'])); ?>">
                        <img src="<?php echo e(asset('assets/images/glo.png')); ?>"
                             class="img-fluid <?php echo e($network == 'GLO' ? 'border border-primary border-3' : ''); ?>"
                             style="width:80px;" alt="Glo" />
                    </a>
                    <a class="me-3" href="<?php echo e(route('admin.network-settings.index', ['network' => '9MOBILE'])); ?>">
                        <img src="<?php echo e(asset('assets/images/9mobile.png')); ?>"
                             class="img-fluid <?php echo e($network == '9MOBILE' ? 'border border-primary border-3' : ''); ?>"
                             style="width:80px;" alt="9Mobile" />
                    </a>
                </div>
            </div>

            <div class="box-body">
                <form method="post" action="<?php echo e(route('admin.network-settings.update')); ?>" class="row">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="col-md-12">
                        <h5><b><?php echo e($network); ?> Network Status</b></h5>
                        <div class="alert alert-info">Use This Section To Enable or Disable A Network Service.</div>
                        <hr/>
                    </div>

                    <!-- Service Status Controls -->
                    <div class="form-group col-md-4">
                        <label for="general" class="form-label fw-bold"><?php echo e($network); ?> General (All)</label>
                        <select name="general" class="form-control" required>
                            <option value="On" <?php echo e($networkData->networkStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->networkStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="vtuStatus" class="form-label fw-bold"><?php echo e($network); ?> Airtime (VTU)</label>
                        <select name="vtuStatus" class="form-control" required>
                            <option value="On" <?php echo e($networkData->vtuStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->vtuStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="sharesellStatus" class="form-label fw-bold"><?php echo e($network); ?> Airtime (Share & Sell)</label>
                        <select name="sharesellStatus" class="form-control" required>
                            <option value="On" <?php echo e($networkData->sharesellStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->sharesellStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="sme" class="form-label fw-bold"><?php echo e($network); ?> SME</label>
                        <select name="sme" class="form-control" required>
                            <option value="On" <?php echo e($networkData->smeStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->smeStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="gifting" class="form-label fw-bold"><?php echo e($network); ?> Gifting</label>
                        <select name="gifting" class="form-control" required>
                            <option value="On" <?php echo e($networkData->giftingStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->giftingStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="corporate" class="form-label fw-bold"><?php echo e($network); ?> Corporate</label>
                        <select name="corporate" class="form-control" required>
                            <option value="On" <?php echo e($networkData->corporateStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->corporateStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="airtimepin" class="form-label fw-bold"><?php echo e($network); ?> Recharge Card</label>
                        <select name="airtimepin" class="form-control" required>
                            <option value="On" <?php echo e($networkData->airtimepinStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->airtimepinStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="datapin" class="form-label fw-bold"><?php echo e($network); ?> Data Pin</label>
                        <select name="datapin" class="form-control" required>
                            <option value="On" <?php echo e($networkData->datapinStatus == 'On' ? 'selected' : ''); ?>>Enable</option>
                            <option value="Off" <?php echo e($networkData->datapinStatus == 'Off' ? 'selected' : ''); ?>>Disable</option>
                        </select>
                    </div>

                    <!-- Network IDs Section -->
                    <div class="col-md-12">
                        <hr/>
                        <h5><b><?php echo e($network); ?> Network ID</b></h5>
                        <div class="alert alert-danger">Use This Section To Change The Network ID Of A Service.</div>
                        <hr/>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="networkid" class="form-label fw-bold"><?php echo e($network); ?> General ID</label>
                        <input type="number" name="networkid" value="<?php echo e($networkData->networkid); ?>"
                               class="form-control" placeholder="General ID" required />
                    </div>

                    <div class="form-group col-md-4">
                        <label for="smeId" class="form-label fw-bold"><?php echo e($network); ?> SME ID</label>
                        <input type="number" name="smeId" value="<?php echo e($networkData->smeId); ?>"
                               class="form-control" placeholder="SME ID" required />
                    </div>

                    <div class="form-group col-md-4">
                        <label for="giftingId" class="form-label fw-bold"><?php echo e($network); ?> Gifting ID</label>
                        <input type="number" name="giftingId" value="<?php echo e($networkData->giftingId); ?>"
                               class="form-control" placeholder="Gifting ID" required />
                    </div>

                    <div class="form-group col-md-4">
                        <label for="corporateId" class="form-label fw-bold"><?php echo e($network); ?> Corporate ID</label>
                        <input type="number" name="corporateId" value="<?php echo e($networkData->corporateId); ?>"
                               class="form-control" placeholder="Corporate ID" required />
                    </div>

                    <div class="form-group col-md-4">
                        <label for="vtuId" class="form-label fw-bold"><?php echo e($network); ?> VTU ID</label>
                        <input type="number" name="vtuId" value="<?php echo e($networkData->vtuId); ?>"
                               class="form-control" placeholder="VTU ID" required />
                    </div>

                    <div class="form-group col-md-4">
                        <label for="sharesellId" class="form-label fw-bold"><?php echo e($network); ?> Share & Sell ID</label>
                        <input type="number" name="sharesellId" value="<?php echo e($networkData->sharesellId); ?>"
                               class="form-control" placeholder="Share & Sell ID" required />
                    </div>

                    <input type="hidden" name="network" value="<?php echo e($networkData->nId); ?>" />

                    <div class="form-group col-md-12">
                        <button type="submit" name="update-network-setting" class="btn btn-primary">
                            <i class="fa fa-save" aria-hidden="true"></i> Update <?php echo e($network); ?> Settings
                        </button>

                        <button type="button" class="btn btn-success ms-2" onclick="testNetworkServices('<?php echo e($network); ?>')">
                            <i class="fa fa-check-circle" aria-hidden="true"></i> Test Services
                        </button>

                        <button type="button" class="btn btn-info ms-2" onclick="showServiceAnalytics('<?php echo e($network); ?>')">
                            <i class="fa fa-chart-bar" aria-hidden="true"></i> View Analytics
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Service Analytics Modal -->
<div class="modal fade" id="analyticsModal" tabindex="-1" aria-labelledby="analyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="analyticsModalLabel">Network Service Analytics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="analyticsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function testNetworkServices(network) {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
    btn.disabled = true;

    fetch(`<?php echo e(route('admin.network-settings.status')); ?>?network=${network}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const services = data.services || [];
                const serviceStatus = services.length > 0
                    ? `Available services: ${services.join(', ')}`
                    : 'No active services';

                alert(`${network} Network Test Results:\n\n${serviceStatus}`);
            } else {
                alert('Network test failed');
            }
        })
        .catch(error => {
            console.error('Test error:', error);
            alert('Network test failed');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

function showServiceAnalytics(network) {
    const modal = new bootstrap.Modal(document.getElementById('analyticsModal'));
    document.getElementById('analyticsModalLabel').textContent = `${network} Service Analytics`;

    fetch(`<?php echo e(route('admin.network-settings.analytics')); ?>?network=${network}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const analytics = data.analytics;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Total Transactions</h5>
                                    <h3 class="text-primary">${analytics.total_transactions}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Success Rate</h5>
                                    <h3 class="text-success">${analytics.success_rate}%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Revenue</h5>
                                    <h3 class="text-info">₦${analytics.revenue}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Failed Transactions</h5>
                                    <h3 class="text-danger">${analytics.failed_transactions}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('analyticsContent').innerHTML = content;
            } else {
                document.getElementById('analyticsContent').innerHTML = '<div class="alert alert-danger">Failed to load analytics</div>';
            }
        })
        .catch(error => {
            console.error('Analytics error:', error);
            document.getElementById('analyticsContent').innerHTML = '<div class="alert alert-danger">Failed to load analytics</div>';
        });

    modal.show();
}

// Auto-save functionality
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('select[name^="general"], select[name$="Status"]');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: Auto-save on change
            console.log(`${select.name} changed to ${select.value}`);
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\MrApollos\Documents\work\dbd_vtu_script_with_Nin_verify\laravel_version\resources\views/admin/network-settings/index.blade.php ENDPATH**/ ?>