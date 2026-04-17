import java.sql.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.HashMap;
import java.util.Map;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.Properties;

public class CampusFixWatchdog {

    // Default connection settings (Works for your XAMPP)
    private static String DB_URL = "jdbc:mysql://localhost:3306/campusfix_db";
    private static String DB_USER = "root";
    private static String DB_PASSWORD = ""; // Default XAMPP is empty

    private static final int POLL_INTERVAL_SECONDS = 30;
    private static final int CRITICAL_PRIORITY_THRESHOLD = 80;
    private static Map<Integer, String> ticketStatusCache = new HashMap<>();
    private static final DateTimeFormatter TIME_FORMAT = DateTimeFormatter.ofPattern("hh:mm a");

    public static void main(String[] args) {
        System.out.println("=== CampusFix Watchdog Service ===");
        System.out.println("Monitoring Priority > " + CRITICAL_PRIORITY_THRESHOLD);

        // Load Config if available, otherwise use defaults
        loadConfiguration();

        if (!testDatabaseConnection()) {
            System.err.println("[ERROR] Could not connect to database. Check XAMPP.");
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
        // Look for config in the same folder
        try (FileInputStream fis = new FileInputStream("config.properties")) {
            props.load(fis);
            DB_USER = props.getProperty("db.user", "root");
            DB_PASSWORD = props.getProperty("db.password", "");
            return true;
        } catch (IOException e) {
            System.out.println("[WARN] No config.properties found. Using default XAMPP settings.");
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
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery("SELECT Ticket_ID, Status FROM tickets")) {
            while (rs.next()) {
                ticketStatusCache.put(rs.getInt("Ticket_ID"), rs.getString("Status"));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private static void monitorDatabase() {
        // Check for Critical Tickets
        String query = "SELECT * FROM tickets WHERE Priority_Score > ? AND Status != 'Resolved'";
        try (Connection conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);
             PreparedStatement pstmt = conn.prepareStatement(query)) {
            pstmt.setInt(1, CRITICAL_PRIORITY_THRESHOLD);
            try (ResultSet rs = pstmt.executeQuery()) {
                while (rs.next()) {
                    System.out.println("[ALERT " + getCurrentTime() + "]: CRITICAL ISSUE! ID: " + 
                        rs.getInt("Ticket_ID") + " - " + rs.getString("Issue_Description"));
                }
            }
        } catch (SQLException e) {
            System.err.println(e.getMessage());
        }
    }

    private static String getCurrentTime() {
        return LocalDateTime.now().format(TIME_FORMAT);
    }
}