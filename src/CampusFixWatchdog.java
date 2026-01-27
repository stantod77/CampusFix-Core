import java.sql.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.HashMap;
import java.util.Map;

/**
 * CampusFix Database Watchdog Service
 * Monitors the campusfix_db for critical events and alerts administrators
 *
 */
class CampusFixWatchdog {

    // Database connection parameters
    private static final String DB_URL = "jdbc:mysql://localhost:3306/campusfix_db";
    private static final String DB_USER = "root"; // Change as needed
    private static final String DB_PASSWORD = ""; // Change as needed

    // Monitoring parameters
    private static final int POLL_INTERVAL_SECONDS = 30;
    private static final int CRITICAL_PRIORITY_THRESHOLD = 80;

    // Track ticket statuses to detect changes
    private static Map<Integer, String> ticketStatusCache = new HashMap<>();

    // Date formatter for console output
    private static final DateTimeFormatter TIME_FORMAT =
            DateTimeFormatter.ofPattern("hh:mm a");

    public static void main(String[] args) {
        System.out.println("===========================================");
        System.out.println("  CampusFix Watchdog Service  ");
        System.out.println("===========================================");
        System.out.println("Monitoring interval: " + POLL_INTERVAL_SECONDS + " seconds");
        System.out.println("Critical priority threshold: " + CRITICAL_PRIORITY_THRESHOLD);
        System.out.println("-------------------------------------------\n");

        // Test database connection
        if (!testDatabaseConnection()) {
            System.err.println("[ERROR] Failed to connect to database. Exiting.");
            return;
        }

        System.out.println("[INFO " + getCurrentTime() + "]: Database connection successful.");
        System.out.println("[INFO " + getCurrentTime() + "]: Watchdog service is now monitoring...\n");

        // Initialize status cache
        initializeStatusCache();

        // Main watchdog loop
        while (true) {
            try {
                monitorDatabase();
                Thread.sleep(POLL_INTERVAL_SECONDS * 1000);
            } catch (InterruptedException e) {
                System.err.println("[ERROR " + getCurrentTime() + "]: Watchdog interrupted. Shutting down.");
                break;
            } catch (Exception e) {
                System.err.println("[ERROR " + getCurrentTime() + "]: Unexpected error - " + e.getMessage());
                e.printStackTrace();
            }
        }
    }
    /**
     * Load database configuration from config.properties
     *
    private static boolean loadConfiguration() {
        Properties props = new Properties();
        try (FileInputStream fis = new FileInputStream("config.properties")) {
            props.load(fis);

            String host = props.getProperty("db.host", "localhost");
            String port = props.getProperty("db.port", "3306");
            String dbname = props.getProperty("db.name", "campusfix_db");
            DB_USER = props.getProperty("db.user");
            DB_PASSWORD = props.getProperty("db.password");

            DB_URL = "jdbc:mysql://" + host + ":" + port + "/" + dbname;

            if (DB_USER == null || DB_PASSWORD == null) {
                System.err.println("[ERROR] Database credentials not found in config.properties");
                return false;
            }

            return true;
        } catch (IOException e) {
            System.err.println("[ERROR] Could not load config.properties: " + e.getMessage());
            return false;
        }
    }
    */

    /**
     * Test database connectivity
     */
    private static boolean testDatabaseConnection() {
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            return conn != null && !conn.isClosed();
        } catch (SQLException e) {
            System.err.println("[ERROR] Database connection failed: " + e.getMessage());
            System.err.println("Please verify:");
            System.err.println("  1. MySQL server is running");
            System.err.println("  2. Database 'campusfix_db' exists");
            System.err.println("  3. Credentials are correct");
            System.err.println("  4. MySQL Connector/J JAR is in classpath");
            return false;
        }
    }

    /**
     * Initialize the status cache with current ticket statuses
     */
    private static void initializeStatusCache() {
        String query = "SELECT Ticket_ID, Status FROM tickets";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {

            while (rs.next()) {
                int ticketId = rs.getInt("Ticket_ID");
                String status = rs.getString("Status");
                ticketStatusCache.put(ticketId, status);
            }

            System.out.println("[INFO " + getCurrentTime() + "]: Initialized tracking for " +
                    ticketStatusCache.size() + " tickets.\n");

        } catch (SQLException e) {
            System.err.println("[ERROR " + getCurrentTime() + "]: Failed to initialize status cache - " +
                    e.getMessage());
        }
    }

    /**
     * Main monitoring logic - checks for critical events
     */
    private static void monitorDatabase() {
        checkCriticalPriorityTickets();
        checkResolvedTickets();
    }

    /**
     * Check for tickets with Priority_Score > 80
     */
    private static void checkCriticalPriorityTickets() {
        String query = "SELECT Ticket_ID, Issue_Description, Location, Priority_Score, Status " +
                "FROM tickets " +
                "WHERE Priority_Score > ? AND Status != 'Resolved'";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement pstmt = conn.prepareStatement(query)) {

            pstmt.setInt(1, CRITICAL_PRIORITY_THRESHOLD);

            try (ResultSet rs = pstmt.executeQuery()) {
                while (rs.next()) {
                    int ticketId = rs.getInt("Ticket_ID");
                    String description = rs.getString("Issue_Description");
                    String location = rs.getString("Location");
                    int priorityScore = rs.getInt("Priority_Score");
                    String status = rs.getString("Status");

                    // Alert for critical priority
                    System.out.println(String.format(
                            "[ALERT %s]: High Priority Issue Detected! (ID: %d, Location: %s)",
                            getCurrentTime(), ticketId, location
                    ));
                    System.out.println(String.format(
                            "              Priority Score: %d | Status: %s",
                            priorityScore, status
                    ));
                    System.out.println(String.format(
                            "              Description: %s\n",
                            truncate(description, 60)
                    ));
                }
            }

        } catch (SQLException e) {
            System.err.println("[ERROR " + getCurrentTime() + "]: Failed to check critical tickets - " +
                    e.getMessage());
        }
    }

    /**
     * Check for tickets that changed status to "Resolved"
     */
    private static void checkResolvedTickets() {
        String query = "SELECT Ticket_ID, Status, Assigned_To FROM tickets";

        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {

            while (rs.next()) {
                int ticketId = rs.getInt("Ticket_ID");
                String currentStatus = rs.getString("Status");
                String assignedTo = rs.getString("Assigned_To");

                // Check if status changed to "Resolved"
                String previousStatus = ticketStatusCache.get(ticketId);

                if (previousStatus != null && !previousStatus.equals("Resolved") &&
                        currentStatus.equals("Resolved")) {

                    // Alert for newly resolved ticket
                    String technician = (assignedTo != null && !assignedTo.isEmpty())
                            ? assignedTo : "Unknown Technician";

                    System.out.println(String.format(
                            "[INFO %s]: Ticket #%d marked as Resolved by %s\n",
                            getCurrentTime(), ticketId, technician
                    ));
                }

                // Update cache with current status
                ticketStatusCache.put(ticketId, currentStatus);
            }

        } catch (SQLException e) {
            System.err.println("[ERROR " + getCurrentTime() + "]: Failed to check resolved tickets - " +
                    e.getMessage());
        }
    }
    /**
     * Get current time
     */
    private static String getCurrentTime() {
        return LocalDateTime.now().format(TIME_FORMAT);
    }

    /**
     * Truncate string
     */
    private static String truncate(String str, int maxLength) {
        if (str == null) return "";
        if (str.length() <= maxLength) return str;
        return str.substring(0, maxLength - 3) + "...";
    }
}