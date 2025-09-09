<?php
// BGP Looking Glass Backend Processing
// This file handles BGP query requests from the custom BGP block

if (isset($_POST['bgpQuery']) && $_POST['bgpQuery'] === '1') {
    header('Content-Type: application/json');
    
    // Validate CSRF token
    if (!isset($_POST['csrfToken']) || !isset($_SESSION['lg_csrf_token']) || ($_POST['csrfToken'] !== $_SESSION['lg_csrf_token'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }

    // Validate input
    if (!isset($_POST['bgpTarget']) || !isset($_POST['bgpMethod'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        exit;
    }

    $target = trim($_POST['bgpTarget']);
    $method = $_POST['bgpMethod'];
    
    // Validate target input
    if (empty($target)) {
        echo json_encode(['success' => false, 'error' => 'Target cannot be empty']);
        exit;
    }

    // Sanitize input - only allow alphanumeric, dots, colons, slashes, and dashes
    if (!preg_match('/^[a-zA-Z0-9\.\:\/-]+$/', $target)) {
        echo json_encode(['success' => false, 'error' => 'Invalid characters in target']);
        exit;
    }

    // Execute BGP query based on method
    $output = '';
    $success = false;

    try {
        switch ($method) {
            case 'route':
                $output = executeBgpRouteQuery($target);
                $success = true;
                break;
            case 'aspath':
                $output = executeBgpAsPathQuery($target);
                $success = true;
                break;
            case 'prefix':
                $output = executeBgpPrefixQuery($target);
                $success = true;
                break;
            case 'asinfo':
                $output = executeBgpAsInfoQuery($target);
                $success = true;
                break;
            default:
                $output = 'Invalid BGP query method';
                $success = false;
        }
    } catch (Exception $e) {
        $output = 'Error executing BGP query: ' . $e->getMessage();
        $success = false;
    }

    echo json_encode([
        'success' => $success,
        'output' => $output
    ]);
    exit;
}

function executeBgpRouteQuery($target) {
    // Try multiple BGP tools and APIs
    
    // First try with whois for route information
    $whoisOutput = shell_exec("whois -h whois.radb.net '$target' 2>/dev/null");
    if (!empty($whoisOutput)) {
        return "Route Information for $target:\n\n" . $whoisOutput;
    }
    
    // Try with curl to bgp.tools API
    $bgpToolsUrl = "https://bgp.tools/prefix/$target";
    $curlOutput = shell_exec("curl -s --max-time 10 '$bgpToolsUrl' 2>/dev/null");
    if (!empty($curlOutput)) {
        return "Route lookup results for $target:\n\nFor detailed information, visit: $bgpToolsUrl\n\nNote: This is a basic route lookup. For real-time BGP table queries, additional BGP daemon integration would be required.";
    }
    
    return "Route lookup for $target:\n\nNote: Direct BGP table access requires additional configuration.\nFor detailed route information, please use the external BGP tools provided above.\n\nTo enable full BGP looking glass functionality, consider integrating with:\n- BIRD BGP daemon\n- Quagga/FRR routing software\n- Direct BGP table access";
}

function executeBgpAsPathQuery($target) {
    // Extract AS number if target contains AS prefix
    $asNumber = preg_replace('/^AS/i', '', $target);
    
    if (is_numeric($asNumber)) {
        return "AS Path information for AS$asNumber:\n\nNote: Real-time AS path queries require integration with BGP routing daemons.\n\nFor current AS path information, please use:\n- Hurricane Electric BGP Toolkit: https://bgp.he.net/AS$asNumber\n- BGP.tools: https://bgp.tools/as/$asNumber\n- RIPE Stat: https://stat.ripe.net/AS$asNumber";
    }
    
    return "AS Path query for $target:\n\nPlease provide a valid AS number (e.g., 212762 or AS212762)";
}

function executeBgpPrefixQuery($target) {
    // Validate if target looks like a network prefix
    if (strpos($target, '/') !== false) {
        $parts = explode('/', $target);
        if (count($parts) === 2 && is_numeric($parts[1])) {
            return "Prefix Information for $target:\n\nPrefix: $target\nType: " . (strpos($target, ':') !== false ? 'IPv6' : 'IPv4') . "\n\nFor detailed prefix analysis:\n- BGP.tools: https://bgp.tools/prefix/" . urlencode($target) . "\n- Hurricane Electric: https://bgp.he.net/net/" . urlencode($parts[0]) . "\n\nNote: Real-time prefix analysis requires BGP table access.";
        }
    }
    
    return "Prefix query for $target:\n\nPlease provide a valid network prefix (e.g., 146.103.47.0/24 or 2a05:dfc1:5500::/40)";
}

function executeBgpAsInfoQuery($target) {
    // Extract AS number
    $asNumber = preg_replace('/^AS/i', '', $target);
    
    if (is_numeric($asNumber)) {
        // Try to get basic AS info using whois
        $whoisOutput = shell_exec("whois -h whois.radb.net 'AS$asNumber' 2>/dev/null");
        
        if (!empty($whoisOutput)) {
            return "AS Information for AS$asNumber:\n\n" . $whoisOutput;
        }
        
        return "AS Information for AS$asNumber:\n\nFor comprehensive AS information, visit:\n- PeeringDB: https://www.peeringdb.com/asn/$asNumber\n- Hurricane Electric: https://bgp.he.net/AS$asNumber\n- RIPE Database: https://apps.db.ripe.net/db-web-ui/query?searchtext=AS$asNumber\n\nNote: Real-time AS information requires integration with routing databases.";
    }
    
    return "AS Information query for $target:\n\nPlease provide a valid AS number (e.g., 212762 or AS212762)";
}
?>
