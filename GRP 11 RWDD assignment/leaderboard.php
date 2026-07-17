<?php
$pageTitle = "Leaderboard"; 
include "conn.php";
$conn = $dbConn;
include "admin_layout_header.php";
include_once "system_alert.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['assign_ranking'])) {

        $userID = $_POST['user_id'];
        $competitionID = $_POST['competition_id'];
        $score = $_POST['score'] ?? 0;
        $rank = $_POST['rank_position'];

        $check = mysqli_query(
            $conn,
            "SELECT 1 FROM leaderboards 
             WHERE UserID='$userID' AND CompetitionID='$competitionID'"
        );

        if (mysqli_num_rows($check)) {
            mysqli_query(
                $conn,
                "UPDATE leaderboards 
                 SET Score='$score', RankPosition='$rank'
                 WHERE UserID='$userID' AND CompetitionID='$competitionID'"
            );
        } else {
            mysqli_query(
                $conn,
                "INSERT INTO leaderboards 
                 (UserID, CompetitionID, Score, RankPosition, Timestamp)
                 VALUES ('$userID', '$competitionID', '$score', '$rank', NOW())"
            );
        }

        header("Location: leaderboard.php?success=ranking");
        exit;
    }

    if (isset($_POST['add_prize'])) {

        mysqli_query(
            $conn,
            "INSERT INTO prizes
            (PrizePoolID, PrizeName, PrizeDescription, PrizeValue, RankAwarded)
            VALUES (
                '{$_POST['prizepool_id']}',
                '{$_POST['prize_name']}',
                '{$_POST['prize_desc']}',
                '{$_POST['prize_value']}',
                '{$_POST['rank_awarded']}'
            )"
        );

        header("Location: leaderboard.php?success=prize");
        exit;
    }

    if (isset($_POST['add_prizepool'])) {

        mysqli_query(
            $conn,
            "INSERT INTO prizepool
            (CompetitionID, PoolName, TotalPrizeValue, CreatedBy, CreatedDate)
            VALUES (
                '{$_POST['competition_id']}',
                '{$_POST['pool_name']}',
                '{$_POST['total_value']}',
                '{$_POST['created_by']}',
                NOW()
            )"
        );

        header("Location: leaderboard.php?success=pool");
        exit;
    }
}

$selectedCompetition = $_GET['competition_id'] ?? '';

$competitions = mysqli_query(
    $conn,
    "SELECT CompetitionID, Title FROM competition ORDER BY ApprovalDate DESC"
);

$where = $selectedCompetition
    ? "WHERE l.CompetitionID='$selectedCompetition'"
    : "";

$leaderboardData = mysqli_query(
    $conn,
    "SELECT l.RankPosition,
            u.Username,
            l.Score,
            c.Title AS CompetitionTitle,
            p.PrizeValue
     FROM leaderboards l
     JOIN users u ON l.UserID = u.UserID
     JOIN competition c ON l.CompetitionID = c.CompetitionID
     LEFT JOIN prizes p ON p.RankAwarded = l.RankPosition
                       AND p.PrizePoolID IN (SELECT PrizePoolID FROM prizepool WHERE CompetitionID = l.CompetitionID)
     $where
     ORDER BY l.CompetitionID, l.RankPosition ASC"
);

$stats = [
    'competitions' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM competition"))['total'],
];
?>

<div class="main">

    <div class="page-header">
        <h1>Leaderboards</h1>

        <div class="toolbar">
            <form method="get">
                <select name="competition_id" onchange="this.form.submit()">
                    <option value="">-- All Competitions --</option>
                    <?php while ($c = mysqli_fetch_assoc($competitions)) { ?>
                        <option value="<?= $c['CompetitionID'] ?>" <?= $selectedCompetition == $c['CompetitionID'] ? 'selected' : '' ?>>
                            <?= $c['Title'] ?>
                        </option>
                    <?php } ?>
                </select>
            </form>

            <button type="button" class="btn" onclick="openModal('assignRankingModal')">Assign Ranking</button>
            <button type="button" class="btn" onclick="openModal('addPrizeModal')">Add Prize</button>
            <button type="button" class="btn" onclick="openModal('addPrizePoolModal')">Add Prize Pool</button>
        </div>
    </div>

    <div class="card-grid">
        <div class="card">Competitions<br><strong><?= $stats['competitions'] ?></strong></div>
    </div>

    <table class="data-table">
        <tr>
            <th>Rank</th>
            <th>User</th>
            <th>Competition</th>
            <th>Score</th>
            <th>Prize</th>
        </tr>
        <?php while ($r = mysqli_fetch_assoc($leaderboardData)) { ?>
            <tr>
                <td><?= $r['RankPosition'] ?></td>
                <td><?= $r['Username'] ?></td>
                <td><?= $r['CompetitionTitle'] ?></td>
                <td><?= $r['Score'] ?></td>
                <td><?= $r['PrizeValue'] ?? '-' ?></td>
            </tr>
        <?php } ?>
    </table>
</div>


<div id="assignRankingModal" class="modal">
    <div class="modal-content">
        <h3>Assign Prizes</h3>
        <form method="post">
            <input type="number" name="user_id" placeholder="User ID" required>
            <select name="competition_id" required>
                <?php
                mysqli_data_seek($competitions, 0);
                while ($c = mysqli_fetch_assoc($competitions)) { ?>
                    <option value="<?= $c['CompetitionID'] ?>"><?= $c['Title'] ?></option>
                <?php } ?>
            </select>
            <input type="number" name="rank_position" placeholder="Rank Position" required>
            <button type="submit" class="btn" name="assign_ranking">Assign</button>
            <button type="button" class="btn danger" onclick="closeModal('assignRankingModal')">Cancel</button>
        </form>
    </div>
</div>

<div id="addPrizeModal" class="modal">
    <div class="modal-content">
        <h3>Add Prize</h3>
        <form method="post">
            <select name="prizepool_id" required>
                <?php
                $pools = mysqli_query($conn, "SELECT PrizePoolID, PoolName FROM prizepool");
                while ($p = mysqli_fetch_assoc($pools)) { ?>
                    <option value="<?= $p['PrizePoolID'] ?>"><?= $p['PoolName'] ?></option>
                <?php } ?>
            </select>
            <input type="text" name="prize_name" placeholder="Prize Name" required>
            <input type="text" name="prize_desc" placeholder="Description">
            <input type="number" name="prize_value" placeholder="Value" required>
            <input type="number" name="rank_awarded" placeholder="Rank Awarded" required>
            <button type="submit" class="btn" name="add_prize">Add</button>
            <button type="button" class="btn danger" onclick="closeModal('addPrizeModal')">Cancel</button>
        </form>
    </div>
</div>

<div id="addPrizePoolModal" class="modal">
    <div class="modal-content">
        <h3>Add Prize Pool</h3>
        <form method="post">
            <select name="competition_id" required>
                <?php
                mysqli_data_seek($competitions, 0);
                while ($c = mysqli_fetch_assoc($competitions)) { ?>
                    <option value="<?= $c['CompetitionID'] ?>"><?= $c['Title'] ?></option>
                <?php } ?>
            </select>
            <input type="text" name="pool_name" placeholder="Pool Name" required>
            <input type="number" name="total_value" placeholder="Total Value" required>
            <input type="text" name="created_by" placeholder="Created By" required>
            <button type="submit" class="btn" name="add_prizepool">Add</button>
            <button type="button" class="btn danger" onclick="closeModal('addPrizePoolModal')">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>

<?php include "admin_layout_footer.php"; ?>