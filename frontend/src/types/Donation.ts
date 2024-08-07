import {Project} from "@/types/Project";

export interface Donation {
    origin: string;
    value: number;
    createdAt: string;
    project: Project;
}