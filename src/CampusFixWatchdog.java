import java.sql.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.HashMap;
import java.util.Map;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.Properties;

public class CampusFixWatchdog {

    private static String DB_URL = "jdbc:mysql://localhost:3306/campusfix_db";
    private static String DB_USER = "campus_user"; // Updated for Pi
    private static String DB_PASSWORD = "db2026";   // Updated for Pi

    private static final int POLL_INTERVAL_SECONDS = 30;
    private static final int CRITICAL_PRIORITY_THRESHOLD = 80;
    private static Map<Integer, String> ticketStatusCache = new HashMap<>();
    private static final DateTimeFormatter TIME_FORMAT = DateTimeFormatter.ofPattern("hh:mm a");

    public static void main(String[] args) {
        System.out.println("=== CampusFix Watchdog Service ===");
        System.out.println("Monitoring Priority > " + CRITICAL_PRIORITY_THRESHOLD);

        // Load Config if available
        loadConfiguration();

        // FORCE LOAD THE DRIVER (Crucial for Pi)
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            System.err.println("[ERROR] MySQL Driver not found! Ensure the .jar is in the folder.");
            return;
        }

        if (!testDatabaseConnection()) {
            System.err.println("[ERROR] Could not connect to database. Check credentials.");
            return;
        }

        System.out.println("[INFO " + getCurrentTime() + "]: Connected. Monitoring...");
        initializeStatusCache();

        while (true) {
            try {
                monitorDatabase();
                Thread.sleep(POLL_INTERVAL_SECONDS * 1000);
            } catch (Exception e) {
                System.err.println("[ERROR]: " + e.getMessage());
            }
        }
    }

    private static boolean loadConfiguration() {
        Properties props = new Properties();
        try (FileInputStream fis = new FileInputStream("config.properties")) {
            props.load(fis);
            DB_USER = props.getProperty("db.user", "campus_user");
            DB_PASSWORD = props.getProperty("db.password", "db2026");
            return true;
        } catch (IOException e) {
            System.out.println("[WARN] No config.properties found. Using internal credentials.");
            return false;
        }
    }

    private static boolean testDatabaseConnection() {
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD)) {
            return conn != null && !conn.isClosed();
        } catch (SQLException e) {
            System.err.println("[ERROR] DB Connection Failed: " + e.getMessage());
            return false;
        }
    }

    private static void initializeStatusCache() {
        // Updated column names: ticket_id, status
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery("SELECT ticket_id, status FROM tickets")) {
            while (rs.next()) {
                ticketStatusCache.put(rs.getInt("ticket_id"), rs.getString("status"));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private static void monitorDatabase() {
        // Updated column names: ticket_id, description
        String query = "SELECT ticket_id, description FROM tickets WHERE priority_score > ? AND status != 'Resolved'";
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement pstmt = conn.prepareStatement(query)) {
            pstmt.setInt(1, CRITICAL_PRIORITY_THRESHOLD);
            try (ResultSet rs = pstmt.executeQuery()) {
                while (rs.next()) {
                    System.out.println("[ALERT " + getCurrentTime() + "]: CRITICAL ISSUE! ID: " +
                        rs.getInt("ticket_id") + " - " + rs.getString("description"));
                }
            }
        } catch (SQLException e) {
            System.err.println("[QUERY ERROR]: " + e.getMessage());
        }
    }

    private static String getCurrentTime() {
        return LocalDateTime.now().format(TIME_FORMAT);
    }
}
