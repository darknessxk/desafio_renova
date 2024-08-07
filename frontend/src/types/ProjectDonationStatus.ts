export interface ProjectDonationStatus {
    id: number;
    donationCount: number;
    donationTotal: number;
    percentage: number;
    status: "open" | "completed";
}