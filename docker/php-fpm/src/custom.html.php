<?php
// BGP Information Block for CK IT Solutions Looking Glass
$bgp_asn = getenv('BGP_ASN') ?: '212762';
$bgp_asn_name = getenv('BGP_ASN_NAME') ?: 'CK IT Solutions';
$bgp_ipv4_prefixes = getenv('BGP_IPV4_PREFIXES') ?: '146.103.47.0/24';
$bgp_ipv6_prefixes = getenv('BGP_IPV6_PREFIXES') ?: '2a05:dfc1:5500::/40';
$bgp_looking_glass_url = getenv('BGP_LOOKING_GLASS_URL') ?: 'https://lg.ck-itsolutions.nl';
$bgp_peeringdb_url = getenv('BGP_PEERINGDB_URL') ?: 'https://www.peeringdb.com/net/33444';

// Split prefixes if multiple are provided (comma-separated)
$ipv4_prefixes_array = array_map('trim', explode(',', $bgp_ipv4_prefixes));
$ipv6_prefixes_array = array_map('trim', explode(',', $bgp_ipv6_prefixes));
?>

<div class="row pb-5">
    <div class="card shadow-lg">
        <div class="card-body p-3">
            <h1 class="fs-4 card-title mb-4">
                <i class="bi bi-diagram-3" style="margin-right: 8px;"></i>
                BGP Network Information
            </h1>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="mb-2 text-muted">Autonomous System Number</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($bgp_asn); ?>" onfocus="this.select()" readonly="">
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo htmlspecialchars($bgp_asn); ?>', this)">Copy</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="mb-2 text-muted">ASN Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($bgp_asn_name); ?>" onfocus="this.select()" readonly="">
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo htmlspecialchars($bgp_asn_name); ?>', this)">Copy</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="mb-2 text-muted">Network Information</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="PeeringDB" onfocus="this.select()" readonly="">
                        <a href="<?php echo htmlspecialchars($bgp_peeringdb_url); ?>" class="btn btn-outline-secondary" target="_blank">View</a>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="mb-2 text-muted">IPv4 Prefixes</label>
                    <?php foreach ($ipv4_prefixes_array as $prefix): ?>
                        <?php if (!empty(trim($prefix))): ?>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(trim($prefix)); ?>" onfocus="this.select()" readonly="">
                                <button class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo htmlspecialchars(trim($prefix)); ?>', this)">Copy</button>
                                <a href="https://bgp.tools/prefix/<?php echo urlencode(trim($prefix)); ?>" class="btn btn-outline-secondary" target="_blank" title="View on BGP.tools">BGP</a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-6">
                    <label class="mb-2 text-muted">IPv6 Prefixes</label>
                    <?php foreach ($ipv6_prefixes_array as $prefix): ?>
                        <?php if (!empty(trim($prefix))): ?>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars(trim($prefix)); ?>" onfocus="this.select()" readonly="">
                                <button class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo htmlspecialchars(trim($prefix)); ?>', this)">Copy</button>
                                <a href="https://bgp.tools/prefix/<?php echo urlencode(trim($prefix)); ?>" class="btn btn-outline-secondary" target="_blank" title="View on BGP.tools">BGP</a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="mb-2 text-muted">External BGP Tools</label>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://bgp.tools/as/<?php echo str_replace('AS', '', $bgp_asn); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-graph-up"></i> BGP.tools
                        </a>
                        <a href="https://bgp.he.net/AS<?php echo str_replace('AS', '', $bgp_asn); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-globe"></i> Hurricane Electric
                        </a>
                        <a href="https://stat.ripe.net/AS<?php echo str_replace('AS', '', $bgp_asn); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-bar-chart"></i> RIPE Stat
                        </a>
                        <a href="<?php echo htmlspecialchars($bgp_peeringdb_url); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-database"></i> PeeringDB
                        </a>
                        <a href="https://radar.cloudflare.com/asn/<?php echo str_replace('AS', '', $bgp_asn); ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-cloud"></i> Cloudflare Radar
                        </a>
                    </div>
                </div>
            </div>

            <!-- BGP Looking Glass Tools -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="mb-3">
                        <i class="bi bi-search"></i>
                        BGP Looking Glass Tools
                    </h5>
                </div>
            </div>

            <form method="POST" autocomplete="off" id="bgpForm">
                <input type="hidden" name="csrfToken" value="<?php echo $_SESSION['lg_csrf_token'] ?? ''; ?>">
                <input type="hidden" name="bgpQuery" value="1">

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text">Target</span>
                            <input type="text" class="form-control" placeholder="IP address, prefix, or AS number..." name="bgpTarget" id="bgpTarget" required="">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="input-group">
                            <label class="input-group-text">Query Type</label>
                            <select class="form-select" name="bgpMethod" id="bgpMethod">
                                <option value="route">Route Lookup</option>
                                <option value="aspath">AS Path</option>
                                <option value="prefix">Prefix Info</option>
                                <option value="asinfo">AS Information</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100" id="bgpExecuteButton">
                            Query
                        </button>
                    </div>
                </div>

                <div class="card card-body bg-dark text-light mt-3" style="display: none;" id="bgpOutputCard">
                    <pre id="bgpOutputContent" style="white-space: pre;word-wrap: normal;margin-bottom: 0;padding-bottom: 1rem;"></pre>
                </div>
            </form>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Network Information:</strong> 
                        This autonomous system (AS<?php echo htmlspecialchars($bgp_asn); ?>) is operated by <?php echo htmlspecialchars($bgp_asn_name); ?>. 
                        Use the BGP tools above to query routing information, or the external tools to view detailed network statistics.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* Custom styling for BGP block */
.bi {
    display: inline-block;
    width: 1em;
    height: 1em;
    fill: currentcolor;
}

.btn-outline-primary {
    border-color: #0d6efd;
    color: #0d6efd;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.gap-2 {
    gap: 0.5rem !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bgpForm = document.getElementById('bgpForm');
    const bgpOutputCard = document.getElementById('bgpOutputCard');
    const bgpOutputContent = document.getElementById('bgpOutputContent');
    const bgpExecuteButton = document.getElementById('bgpExecuteButton');

    if (bgpForm) {
        bgpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(bgpForm);
            bgpExecuteButton.disabled = true;
            bgpExecuteButton.textContent = 'Querying...';
            bgpOutputCard.style.display = 'block';
            bgpOutputContent.textContent = 'Executing BGP query, please wait...';

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bgpOutputContent.textContent = data.output;
                } else {
                    bgpOutputContent.textContent = 'Error: ' + (data.error || 'Unknown error occurred');
                }
            })
            .catch(error => {
                bgpOutputContent.textContent = 'Error: Failed to execute BGP query - ' + error.message;
            })
            .finally(() => {
                bgpExecuteButton.disabled = false;
                bgpExecuteButton.textContent = 'Query';
            });
        });
    }
});
</script>
