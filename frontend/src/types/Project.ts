import {User} from "@/types/User";
import {Donation} from "@/types/Donation";
import {ProjectDonationStatus} from "@/types/ProjectDonationStatus";

export interface Project {
    id: number;
    name: string;
    description: string;
    meta: number;
    createdAt: string;
    category: string;
    owner: User;
    donations: Donation[];
    projectDonationStatus: ProjectDonationStatus;
}