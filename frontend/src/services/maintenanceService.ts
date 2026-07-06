// services/maintenanceService.ts
import axiosClient from "./axiosClient";

export interface MaintenanceStatus {
  maintenance_mode: boolean;
  custom_activity_tracker?: boolean;
}

export const checkMaintenanceMode = async (): Promise<boolean> => {
  try {
    const response = await axiosClient.get("/site/header");
    return response.data.data?.status?.maintenance_mode || false;
  } catch (error) {
    console.error("Maintenance check failed:", error);
    return false; // Fallback - agar API fail hua toh site chalu rahe
  }
};
